<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC/The Great Rift Valley Software Company
    
    LICENSE:
    
    MIT License
    
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
    modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
    IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
    CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

    Little Green Viper Software Development: https://littlegreenviper.com
*/
defined( 'LGV_MD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/***************************************************************************************************************************/
/**
This is the instance class for the main "data" database.
 */
class CO_Main_Data_DB extends A_CO_DB {
	
    /***********************************************************************************************************************/
	/*******************************************************************/
	/**
		\brief Uses the Vincenty calculation to determine the distance (in Kilometers) between the two given lat/long pairs (in Degrees).
		
		The Vincenty calculation is more accurate than the Haversine calculation, as it takes into account the "un-spherical" shape of the Earth, but is more computationally intense.
		We use this calculation to refine the Haversine "triage" in SQL.
		
		\returns a Float with the distance, in Kilometers.
	*/
	static function get_accurate_distance (	$lat1,  ///< This is the first point latitude (degrees).
                                            $lon1,  ///< This is the first point longitude (degrees).
                                            $lat2,  ///< This is the second point latitude (degrees).
                                            $lon2   ///< This is the second point longitude (degrees).
                                        )
	{
	    if (($lat1 == $lat2) && ($lon1 == $lon2)) { // Just a quick shortcut.
	        return 0;
	    }
	    
		$a = 6378137;
		$b = 6356752.3142;
		$f = 1/298.257223563;  // WGS-84 ellipsiod
		$L = ($lon2-$lon1)/57.2957795131;
		$U1 = atan((1.0-$f) * tan($lat1/57.2957795131));
		$U2 = atan((1.0-$f) * tan($lat2/57.2957795131));
		$sinU1 = sin($U1);
		$cosU1 = cos($U1);
		$sinU2 = sin($U2);
		$cosU2 = cos($U2);
		  
		$lambda = $L;
		$lambdaP = $L;
		$iterLimit = 100;
		
		do {
			$sinLambda = sin($lambda);
			$cosLambda = cos($lambda);
			$sinSigma = sqrt(($cosU2*$sinLambda) * ($cosU2*$sinLambda) + ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda) * ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda));
    		if ($sinSigma==0) {
    			return 0;  // co-incident points
    		}
    		
			$cosSigma = $sinU1*$sinU2 + ($cosU1*$cosU2*$cosLambda);
			$sigma = atan2($sinSigma, $cosSigma);
			$sinAlpha = ($cosU1 * $cosU2 * $sinLambda) / $sinSigma;
			$cosSqAlpha = 1.0 - $sinAlpha*$sinAlpha;
			
			if (0 == $cosSqAlpha) {
    			return 0;
    		}
    		
			$cos2SigmaM = $cosSigma - 2.0*$sinU1*$sinU2/$cosSqAlpha;
			
			$divisor = (16.0*$cosSqAlpha*(4.0+$f*(4.0-3.0*$cosSqAlpha)));
			
			if (0 == $divisor) {
			    return 0;
			}
			
			$C = $f/$divisor;
			
			$lambdaP = $lambda;
			$lambda = $L + (1.0-$C) * $f * $sinAlpha * ($sigma + $C*$sinSigma*($cos2SigmaM+$C*$cosSigma*(-1.0+2.0*$cos2SigmaM*$cos2SigmaM)));
		} while (abs($lambda-$lambdaP) > 1e-12 && --$iterLimit>0);

		$uSq = $cosSqAlpha * ($a*$a - $b*$b) / ($b*$b);
		$A = 1.0 + $uSq/16384.0*(4096.0+$uSq*(-768.0+$uSq*(320.0-175.0*$uSq)));
		$B = $uSq/1024.0 * (256.0+$uSq*(-128.0+$uSq*(74.0-47.0*$uSq)));
		$deltaSigma = $B*$sinSigma*($cos2SigmaM+$B/4.0*($cosSigma*(-1.0+2.0*$cos2SigmaM*$cos2SigmaM)-$B/6.0*$cos2SigmaM*(-3.0+4.0*$sinSigma*$sinSigma)*(-3.0+4.0*$cos2SigmaM*$cos2SigmaM)));
		$s = $b*$A*($sigma-$deltaSigma);
  		
		return ( abs ( round ( $s ) / 1000.0 ) ); 
	}
	
    /***********************************************************************************************************************/
    /***********************/
    /**
    This method creates a special SQL header that has an embedded Haversine formula. You use this in place of the security predicate.
    
    The Haversine formula is not as accurate as the Vincenty Calculation, but is a lot less computationally intense, so we use this in SQL for a "triage."
    
    \returns an SQL query that will specify a Haversine search. It will include the security predicate.
     */
    protected function _location_predicate( $in_longitude,          ///< The search center longitude, in degrees.
                                            $in_latitude,           ///< The search center latitude, in degrees.
                                            $in_radius_in_km,       ///< The search radius, in Kilometers.
                                            $and_writeable = false, ///< If true, then we only want records we can modify.
                                            $count_only = false     ///< If true (default is false), then only a single integer will be returned, with the count of items that fit the search.
                                            ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        $predicate = $this->_create_security_predicate($and_writeable);

        if (!$predicate) {
            $predicate = 'true'; // If we are in "God Mode," we could get no predicate, so we just go with "1".
        }
    
        $ret['sql'] = $count_only ? 'SELECT COUNT(*) FROM (' : '';
        $ret['sql'] .= "SELECT * FROM (
                        SELECT z.*,
                            p.radius,
                            p.distance_unit
                                     * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                                     * COS(RADIANS(z.latitude))
                                     * COS(RADIANS(p.longpoint - z.longitude))
                                     + SIN(RADIANS(p.latpoint))
                                     * SIN(RADIANS(z.latitude)))) AS distance
                        FROM ".$this->table_name." AS z
                        JOIN (   /* these are the query parameters */
                            SELECT  ".floatval($in_latitude)."  AS latpoint,  ".floatval($in_longitude)." AS longpoint,
                                    ".floatval($in_radius_in_km)." AS radius,      111.045 AS distance_unit
                        ) AS p ON 1=1
                        WHERE z.latitude
                         BETWEEN p.latpoint  - (p.radius / p.distance_unit)
                             AND p.latpoint  + (p.radius / p.distance_unit)
                        AND z.longitude
                         BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
                             AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
                        ) AS d
                        WHERE ($predicate AND ((distance <= radius)";
        
        return $ret;
    }
    
    /***********************/
    /**
    This method will return an SQL statement and a set of parameters for the tags.
    
    \returns an SQL statement that acts as a WHERE clause for the tags.
     */
    protected function _parse_tags( $in_value   ///< This should be an array of string. You can provide just one string, but that will always be applied to tag0.
                                    ) {
        $ret = Array('sql' => '', 'params' => Array());
        if (isset($in_value) && is_array($in_value) && count($in_value)) {
            $use_like = false;
            
            if (isset($in_value['use_like'])) {
                $use_like = true;
                unset($in_value['use_like']);
            }
            
            $sql_temp = Array();
            
            for ($i = 0; $i < count($in_value); $i++) {
                $sql_temp[$i] = '';
                $value = $in_value[$i];
                
                if ((NULL !== $value) && ('%' != $value)) {
                    if (is_array($value) && count($value)) {
                        $use_like_old = $use_like;
                        
                        if (isset($value['use_like'])) {
                            $use_like = true;
                            unset($value['use_like']);
                        }
                        
                        $inner_array = Array();
                        foreach ($value as $val) {
                            if (NULL != $val) {
                                $val = trim(strval($val));

                                if ('' == $val) {
                                    $inner_array[] = '((tag'.intval($i).' IS NULL) OR (tag'.intval($i).'=\'\'))';
                                } elseif ('%' == $val) {
                                    $inner_array[] = '(tag'.intval($i).'<>\'\')';
                                } else {
                                    $like_me = (false !== strpos($val, '%')) && $use_like;
                                
                                    $inner_array[] = 'LOWER(tag'.intval($i).')'.($like_me ? ' LIKE ' : '=').'LOWER(?)';
                                    array_push($ret['params'], $val);
                                }
                            }
                        }
                        
                        if (1 < count($inner_array)) {
                            $sql_temp[$i] = '('.implode(') OR (', $inner_array).')';
                        } elseif (count($inner_array)) {
                            $sql_temp[$i] = $inner_array[0];
                        }
                        
                        $use_like = $use_like_old;
                    } else {
                        if (NULL !== $value) {
                            $value = trim(strval($value));
                        
                            if ('' == $value) {
                                $sql_temp[$i] = '((tag'.intval($i).' IS NULL) OR (tag'.intval($i).'=\'\'))';
                            } else {
                                $like_me = (false !== strpos($value, '%')) && $use_like;
                                
                                $sql_temp[$i] = 'LOWER(tag'.intval($i).')'.($like_me ? ' LIKE ' : '=').'LOWER(?)';
                                array_push($ret['params'], strval($value));
                            }
                        }
                    }
                } elseif ('%' == $value) {
                    $sql_temp[$i] = '(tag'.intval($i).'<>\'\')';
                }
            }
            
            $temp_array = Array();
            
            // Can't just do an array_filter, because PHP likes to keep the filtered elements at their original indexes.
            foreach ($sql_temp as $array_element) {
                if ('' != $array_element) {
                    array_push($temp_array, $array_element);
                }
            }
            if (1 < count($temp_array)) {
                $ret['sql'] = '(('.implode(') AND (', $temp_array).'))';
            } elseif (1 == count($temp_array)) {
                $ret['sql'] = $temp_array[0];
            }
        } else {
            $in_value = trim(strval($in_value));
            if (NULL !== $in_value) {
                if ('' == $in_value) {
                    $ret['sql'] = '((tag0 IS NULL) OR (tag0=\'\'))';
                } else {
                    $like_me = (false !== strpos($in_value, '%')) && $use_like;
                    
                    $ret['sql'] = '(LOWER(tag0)'.($like_me ? ' LIKE ' : '=').'LOWER(?))';
                    array_push($ret['params'], strval($value));
                }
            }
        }

        return $ret;
    }
    
    /***********************/
    /**
    This method will return an SQL statement and an empty set of parameters for an integer table column value.
    
    \returns an SQL statement that acts as a WHERE clause for a integer.
     */
    protected function _parse_integer_parameter(    $in_db_key, ///< The table column name.
                                                    $in_value   ///< The value
                                                ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        if (isset($in_value) && is_array($in_value) && count($in_value)) {
            $in_value = array_unique(array_map('intval', $in_value));    // Make sure we don't have repeats.
            
            $sql_array = Array();

            foreach ($in_value as $value) {                
                if (NULL !== $value) {
                    $sql_array[] = strval($in_db_key).'=?';
                    array_push($ret['params'], $value);
                }
            }
            
            $ret['sql'] = '('.implode(') OR (', $sql_array).')';
        } else {
            $ret['sql'] = ''.strval($in_db_key).'=?';
            array_push($ret['params'], $in_value);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This method will return an SQL statement and a set of parameters for a case-insensitive string table column value.
    
    \returns an SQL statement that acts as a WHERE clause for a string.
     */
    protected function _parse_string_parameter( $in_db_key,
                                                $in_value
                                                ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        if (isset($in_value) && is_array($in_value) && count($in_value)) {
            $use_like = false;
            
            if (isset($in_value['use_like'])) {
                $use_like = true;
                unset($in_value['use_like']);
            }
            
            $in_value = array_unique(array_map(function($in){return strtolower(trim(strval($in)));}, $in_value));    // Make sure we don't have repeats.
            $sql_array = Array();
            
            foreach ($in_value as $value) {                
                if ((NULL != $value) && ('%' != $value)) {
                    $sql_array[] = 'LOWER('.strval($in_db_key).')'.($use_like ? ' LIKE ' : '=').'LOWER(?)';
                    array_push($ret['params'], $value);
                } elseif ('%' == $value) {
                    $sql_array[] = '('.strval($in_db_key).'<>\'\')';
                }
            }
            
            $ret['sql'] = '(('.implode(') OR (', $sql_array).'))';
        } else {
            $ret['sql'] = 'LOWER('.strval($in_db_key).')=LOWER(?)';
            $ret['params'][0] = $in_value;
        }

        return $ret;
    }
    
    /***********************/
    /**
    This parses the provided parameters, and returns a WHERE clause for them.
    
    \returns an SQL statement that acts as a WHERE clause for the given parameters.
     */
    protected function _parse_parameters(   $in_search_parameters = NULL,   /**< This is an associative array of terms to define the search. The keys should be:
                                                                                - 'id'
                                                                                    This should be accompanied by an array of one or more integers, representing specific item IDs.
                                                                                - 'access_class'
                                                                                    This should be accompanied by an array, containing one or more PHP class names (case-insensitive strings).
                                                                                - 'name'
                                                                                    This will contain a case-insensitive array of strings to check against the object_name column.
                                                                                - 'owner'
                                                                                    This should be accompanied by an array of one or more integers, representing specific item IDs for "owner" objects.
                                                                                - 'tags'
                                                                                    This should be accompanied by an array (up to 10 elements) of one or more case-insensitive strings, representing specific tag values.
                                                                            */
                                            $or_search = false              ///< If true, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                        ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        if (isset($in_search_parameters) && is_array($in_search_parameters) && count ($in_search_parameters)) {
            $sql_array = Array();
            $param_array = Array();
            
            foreach ($in_search_parameters as $key => $value) {
                $temp = NULL;
                
                switch ($key) {
                    case 'id':
                        $temp = $this->_parse_integer_parameter('id', $value);
                    break;
                
                    case 'access_class':
                        $temp = $this->_parse_string_parameter('access_class', $value);
                    break;
                
                    case 'name':
                        $temp = $this->_parse_string_parameter('object_name', $value);
                    break;
                
                    case 'owner':
                        $temp = $this->_parse_integer_parameter('owner', $value);
                    break;
                    
                    case 'tags':
                        $temp = $this->_parse_tags($value);
                    break;
                
                    default:
                    break;
                }
                
                if (isset($temp) && is_array($temp) && count($temp)) {
                    $sql_array[] = $temp['sql'];
                    $ret['params'] = array_merge($ret['params'], $temp['params']);
                }
            }
            
            if (1 < count($sql_array)) {
                $link = $or_search ? ') OR (' : ') AND (';
            
                $ret['sql'] = '(('.implode($link, $sql_array).'))';
            } elseif (count($sql_array)) {
                $ret['sql'] = $sql_array[0];
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This builds up an SQL query, based on the input from the user.
    
    \returns an array of instances that match the search parameters.
     */
    protected function _build_sql_query(    $in_search_parameters = NULL,   /**< This is an associative array of terms to define the search. The keys should be:
                                                                                - 'id'
                                                                                    This should be accompanied by an array of one or more integers, representing specific item IDs.
                                                                                - 'access_class'
                                                                                    This should be accompanied by an array, containing one or more PHP class names.
                                                                                - 'name'
                                                                                    This will contain a case-insensitive array of strings to check against the object_name column.
                                                                                - 'owner'
                                                                                    This should be accompanied by an array of one or more integers, representing specific item IDs for "owner" objects.
                                                                                - 'tags'
                                                                                    This should be accompanied by an array (up to 10 elements) of one or more case-insensitive strings, representing specific tag values.
                                                                                - 'location'
                                                                                    This requires that the parameter be a 3-element associative array of floating-point numbers:
                                                                                        - 'longtude'
                                                                                            This is the search center location longitude, in degrees.
                                                                                        - 'latitude'
                                                                                            This is the search center location latitude, in degrees.
                                                                                        - 'radius'
                                                                                            This is the search radius, in Kilometers.
                                                                            */
                                            $or_search = false,             ///< If true, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                            $page_size = 0,                 ///< If specified with a 1-based integer, this denotes the size of a "page" of results. NOTE: This is only applicable to MySQL or Postgres, and will be ignored if the DB is not MySQL or Postgres.
                                            $initial_page = 0,              ///< This is ignored unless $page_size is greater than 0. If so, then this 0-based index will specify which page of results to return.
                                            $and_writeable = false,         ///< If true, then we only want records we can modify.
                                            $count_only = false,            ///< If true (default is false), then only a single integer will be returned, with the count of items that fit the search.
                                            $ids_only = false               ///< If true (default is false), then the return array will consist only of integers (the object IDs). If $count_only is true, this is ignored.
                                        ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        $closure = '';  // This will be the suffix for the SQL.
        $location_search = false;   // We use this as a semaphore, so we don't shortcut location searches you can't refine to only IDs, because of the syntax of the SQL.
        $link = '';
        
        // If we are doing a location/radius search, the predicate is a lot more complicated.
        if (isset($in_search_parameters['location']) && isset($in_search_parameters['location']['longitude']) && isset($in_search_parameters['location']['latitude']) && isset($in_search_parameters['location']['radius'])) {
            // We expand the radius by 5%, because we'll be triaging the results with the more accurate Vincenty calculation afterwards.
            $predicate_temp = $this->_location_predicate($in_search_parameters['location']['longitude'], $in_search_parameters['location']['latitude'], floatval($in_search_parameters['location']['radius']) * 1.02, $and_writeable, $count_only);
            $sql = $predicate_temp['sql'];
            $ret['params'] = $predicate_temp['params'];
            $closure = $count_only ? ')' : ') ORDER BY distance,id';
            $location_search = true;
            $link = ' AND ';
        } else {
            $predicate = $this->_create_security_predicate($and_writeable);
        
            if (!$predicate) {
                $predicate = 'true'; // If we are in "God Mode," we could get no predicate, so we just go with "1".
            }
        
            $sql = $count_only ? 'SELECT COUNT(*) FROM (' : '';
            $sql .= 'SELECT * FROM '.$this->table_name.' WHERE ('.$predicate.' AND (';
            $closure = $count_only ? ')' : ') ORDER BY id';
        }
        
        // At this point, we have the "prefix" for the SQL query. That includes the security predicate, and any Haversine "triage" for location.
        // We now add the actual parameters that specialize the search.
        
        if (isset($in_search_parameters) && is_array($in_search_parameters) && count($in_search_parameters)) {
            // This function will parse the parameters, and return an associative array with the SQL WHERE clause, along with the relevant parameters for the prepared statement.
            $param_ret = $this->_parse_parameters($in_search_parameters, $or_search);
            
            if ($param_ret['sql']) {
                $sql .= $link.$param_ret['sql'];
                if (count($param_ret['params'])) {
                    $ret['params'] = array_merge($ret['params'], $param_ret['params']);
                }
            }
        } else {
            $sql .= 'true';
        }
                
        $closure = ")$closure";
        
        $page_size = intval($page_size);
        // This only applies for MySQL or Postgres.
        if (0 < $page_size) {
            $initial_page = intval($initial_page);
            $start = $initial_page * $page_size;
            // Slightly different syntax for MySQL and Postgres.
            if ( (('mysql' == $this->_pdo_object->driver_type) || ('mysqli' == $this->_pdo_object->driver_type))) {
                $closure .= ' LIMIT '.$start.', '.$page_size;
            } elseif ('pgsql' == $this->_pdo_object->driver_type) {
                $closure .= ' LIMIT '.$page_size.' OFFSET '.$start;
            }
        }
            
        if ($count_only) {
            $closure .= ') AS count';
        } elseif ($ids_only && !$location_search) { // IDs only, we simply ask for only the ID.
            $replacement = 'SELECT (id)';
            $sql = preg_replace('|^SELECT \*|', $replacement, $sql);
        }
        
        $ret['sql'] = $sql.$closure;

        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    The initializer.
     */
	public function __construct(    $in_pdo_object,             ///< The PDO object for this database, initialized and ready.
	                                $in_access_object = NULL    ///< The access object for the database. If NULL, then no login.
                                ) {
        parent::__construct($in_pdo_object, $in_access_object);
        
        $this->table_name = 'co_data_nodes';
        
        $this->class_description = 'The main data database class.';
    }
    
    /***********************/
    /**
    This is a very "raw" function that simply checks to see if any item exists for a given integer ID.
    
    This (usually) deliberately does not pass security vetting, so we're careful. It's meant to be used by collection classes for garbage collection.
    
    \returns true, if an item exists for the given ID (if $in_visibility_test is set to true, then the item also has to be visible for reading by the user. Otherwise, you get true, whether or not the user can see it).
     */
    public function item_exists(    $in_id,                     ///< The ID of the item.
                                    $in_visibility_test = false ///< If true (default is false), then this will return false, even if the item exists, but cannot be seen by this user.
                                ) {
        $ret = NULL;
        
        $sql = 'SELECT id FROM '.$this->table_name.' WHERE ';
    
        if ($in_visibility_test) {  // If we are only testing visibility, then we add a read security predicate.
            $predicate = $this->_create_read_security_predicate();
        
            if ($predicate) {
                $sql = "$sql$predicate AND ";
            }
        }
    
        // User collections work by having the login ID in tag 0, so we search for any collection records that have a tag 0 set to our login ID. Chances are good it's a user.
        $sql .= 'id='.intval($in_id);

        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = true;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a very "raw" function that simply checks to see if a user collection exists for a given login ID.
    
    This deliberately does not pass security vetting, so we're careful.
    
    \returns true, if a user exists for the given login ID.
     */
    public function see_if_user_exists( $in_login_id    ///< The login ID of the user.
                                    ) {
        $ret = NULL;
        
        // User collections work by having the login ID in tag 0, so we search for any collection records that have a tag 0 set to our login ID. Chances are good it's a user.
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE (access_class LIKE \'%_User_Collection\') AND (tag0=\''.intval($in_login_id).'\')';

        $temp = $this->execute_query($sql, Array($in_login_id));
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            // We instantiate, as opposed to check the access_class, because we want to give the implementation the option of subclassing.
            $result = $this->_instantiate_record($temp[0]);
            if ($result instanceof CO_User_Collection) {    // This will crash if we aren't looking at it from a CHAMELEON (at least) level. That's good.
                $ret = true;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns true, if the tag1 is unique in the DB (regardless of whether or not we can see it).
     */
    public function tag0_is_unique( $in_tag0,                                   ///< The key to test (a string).
                                    $in_classname = 'CO_KeyValue_CO_Collection' ///< This is the class to search for the key. The default is the base class.
                                    ) {
        $ret = true;
        
        $sql = 'SELECT id FROM '.$this->table_name.' WHERE (access_class=?) AND (tag0=?)';
        $params = [$in_classname, $in_tag0];
        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = false;
        }
                
        return $ret;
    }
    
    /***********************/
    /**
    This is a "generic" data database search. It can be called from external user contexts, and allows a fairly generalized search of the "data" database.
    Sorting will be done for the values by the ID of the searched objects. "location" will be by distance from the center.
    
    \returns an array of instances that match the search parameters.
     */
    public function generic_search( $in_search_parameters = NULL,   /**< This is an associative array of terms to define the search. The keys should be:
                                                                        - 'id'
                                                                            This should be accompanied by an array of one or more integers, representing specific item IDs.
                                                                        - 'access_class'
                                                                            This should be accompanied by an array, containing one or more PHP class names.
                                                                        - 'name'
                                                                            This will contain a case-insensitive array of strings to check against the object_name column.
                                                                        - 'owner'
                                                                            This should be accompanied by an array of one or more integers, representing specific item IDs for "owner" objects.
                                                                        - 'tags'
                                                                            This should be accompanied by an array (up to 10 elements) of one or more case-insensitive strings, representing specific tag values.
                                                                            The position in the array denotes which tag to match, so unchecked tags should still be in the array, but empty. You don't match empty tags.
                                                                            You can specify an array for the values, which allows you to do an OR search for the values.
                                                                        - 'location'
                                                                            This is only relevant if we are searching for subclasses (or instances) of CO_LL_Location
                                                                            This requires that the parameter be a 3-element associative array of floating-point numbers:
                                                                                - 'longitude'
                                                                                    This is the search center location longitude, in degrees.
                                                                                - 'latitude'
                                                                                    This is the search center location latitude, in degrees.
                                                                                - 'radius'
                                                                                    This is the search radius, in Kilometers.

                                                                        You can specify an array for any one of the values, which allows you to do an OR search for those values ($or_search does not apply. It is only for the combination of main values).
                                                                        If you add an element called 'use_like' ('use_like' => 1) to the end of 'access_class', 'name' or one of the 'tags', then you can use SQL-style "wildcards" (%) in your matches.
                                                                        If you have 'use_like', and put just a single wildcard in quotes ('%'), then you are saying "not-empty."
                                                                        NOTE: Although this is an optional parameter, failing to provide anything could return the entire readable database.
                                                                    */
                                    $or_search = false,             ///< If true, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                    $page_size = 0,                 ///< If specified with a 1-based integer, this denotes the size of a "page" of results. NOTE: This is only applicable to MySQL or Postgres, and will be ignored if the DB is not MySQL or Postgres.
                                    $initial_page = 0,              ///< This is ignored unless $page_size is greater than 0. If so, then this 0-based index will specify which page of results to return.
                                    $and_writeable = false,         ///< If true, then we only want records we can modify.
                                    $count_only = false,            ///< If true (default is false), then only a single integer will be returned, with the count of items that fit the search.
                                    $ids_only = false               ///< If true (default is false), then the return array will consist only of integers (the object IDs). If $count_only is true, this is ignored.
                                    ) {
        $ret = NULL;
        
        // These are semaphores that we'll consult when the dust settles.
        $location_count = $count_only;
        $location_ids_only = $ids_only;
        $location_search = (isset($in_search_parameters['location']) && isset($in_search_parameters['location']['longitude']) && isset($in_search_parameters['location']['latitude']) && isset($in_search_parameters['location']['radius']));
        
        if ($location_search) { // We're forced to use the regular search for count-only and IDs location, as we need that Vincenty filter.
            $count_only = false;
            $ids_only = false;
        }
        
        $sql_and_params = $this->_build_sql_query($in_search_parameters, $or_search, $page_size, $initial_page, $and_writeable, $count_only, $ids_only);
        $sql = $sql_and_params['sql'];
        $params = $sql_and_params['params'];
        
        if ($sql) {
            $temp = $this->execute_query($sql, $params);
// Commented out, but useful for debugging.
// echo('SQL:<pre>'.htmlspecialchars(print_r($sql, true)).'</pre>');
// echo('PARAMS:<pre>'.htmlspecialchars(print_r($params, true)).'</pre>');
// echo('RESULT:<pre>'.htmlspecialchars(print_r($temp, true)).'</pre>');
            if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                if ($count_only) {  // Different syntax for MySQL and Postgres
                    if (isset($temp[0]['count(*)'])) {
                        $ret = intval($temp[0]['count(*)']);
                    } else {
                        if (isset($temp[0]['count'])) {
                            $ret = intval($temp[0]['count']);
                        }
                    }
                } else {
                    $ret = Array();
                    foreach ($temp as $result) {
                        $result = $ids_only ? intval($result['id']) : $this->_instantiate_record($result);
                        if ($result) {
                            array_push($ret, $result);
                        }
                    }

                    // If we do a distance search, then we filter and sort the results with the more accurate Vincenty algorithm, and we also give each record a "distance" parameter.
                    if ($location_search) {
                        $ret_temp = Array();
                        $count = 0;
                        
                        foreach ($ret as $item) {
                            $latitude = floatval($item->raw_latitude());    // This allows logins with the rights to see accurate locations an accurate response.
                            $longitude = floatval($item->raw_longitude());
                            
                            $accurate_distance = self::get_accurate_distance(floatval($in_search_parameters['location']['latitude']), floatval($in_search_parameters['location']['longitude']), $latitude, $longitude);
                            if ($accurate_distance <= floatval($in_search_parameters['location']['radius'])) {
                                $item->distance = $accurate_distance;
                                array_push($ret_temp, $item);
                                $count++;
                            }
                        }
                        
                        if ($location_count) {
                            $ret_temp = $count;
                        } else {
                            usort($ret_temp, function($a, $b){return ($a->distance > $b->distance);});
                        
                            if ($location_ids_only) {
                                $ret_temp = array_map(function($in_item) { return $in_item->id(); }, $ret_temp);
                            }
                        }

                        $ret = $ret_temp;
                    }
                }
            }
        }
        
        return $ret;
    } 
};
