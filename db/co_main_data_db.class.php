<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC.
    
    This code is proprietary and confidential code, 
    It is NOT to be reused or combined into any application,
    unless done so, specifically under written license from Little Green Viper Software Development LLC.

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
		\brief Uses the Vincenty calculation to determine the distance (in KM) between the two given lat/long pairs (in Degrees).
		
		\returns a Float with the distance, in kilometers.
	*/
	static function get_accurate_distance (	$lat1,  ///< These four parameters are the given two points long/lat, in degrees.
                                            $lon1,
                                            $lat2,
                                            $lon2
                                        )
	{
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
    		if ($sinSigma==0)
    			{
    			return true;  // co-incident points
    			}
			$cosSigma = $sinU1*$sinU2 + ($cosU1*$cosU2*$cosLambda);
			$sigma = atan2($sinSigma, $cosSigma);
			$sinAlpha = ($cosU1 * $cosU2 * $sinLambda) / $sinSigma;
			$cosSqAlpha = 1.0 - $sinAlpha*$sinAlpha;
			$cos2SigmaM = $cosSigma - 2.0*$sinU1*$sinU2/$cosSqAlpha;
			$C = $f/(16.0*$cosSqAlpha*(4.0+$f*(4.0-3.0*$cosSqAlpha)));
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
    
    \returns an SQL query that will specify a Haversine search. It will include the security predicate.
     */
    protected function _location_predicate( $in_longitude,          ///< The search center longitude, in degrees.
                                            $in_latitude,           ///< The search center latitude, in degrees.
                                            $in_radius_in_km,       ///< The search radius, in Kilometers.
                                            $and_writeable = FALSE, ///< If TRUE, then we only want records we can modify.
                                            $count_only = FALSE     ///< If TRUE (default is FALSE), then only a single integer will be returned, with the count of items that fit the search.
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
                        WHERE (($predicate) AND (distance <= radius)";
        
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
            $use_like = FALSE;
            
            if (isset($in_value['use_like'])) {
                $use_like = TRUE;
                unset($in_value['use_like']);
            }

            for ($i = 0; $i < count($in_value); $i++) {
                $value = $in_value[$i];
                
                if ($value) {
                    if (isset($value) && is_array($value) && count($value)) {
                        $use_like_old = $use_like;
                        
                        if (isset($value['use_like'])) {
                            $use_like = TRUE;
                            unset($value['use_like']);
                        }

                        $i2 = 0;
                        foreach ($value as $val) {                
                            $val = trim(strval($val));
                            if ($val) {
                                if ($ret['sql']) {
                                    $ret['sql'] .= ') OR ';
                                }
                    
                                $ret['sql'] .= '(LOWER(tag'.intval($i).')'.($use_like ? ' LIKE ' : '=').'LOWER(?)';
                                array_push($ret['params'], $val);
                            }
                            
                            $i2++;
                        }
                        $use_like = $use_like_old;
                    } else {
                        $value = trim(strval($value));
                        
                        if ($value) {
                            if ($ret['sql']) {
                                $ret['sql'] .= ') OR ';
                            }
                    
                            $ret['sql'] .= '(LOWER(tag'.intval($i).')'.($use_like ? ' LIKE ' : '=').'LOWER(?)';
                            array_push($ret['params'], strval($value));
                        }
                    }
                }
            }
            
            if ($ret['sql']) {
                $ret['sql'] .= ')';
            }
        } else {
            $in_value = trim(strval($in_value));
            if ($in_value) {
                $ret['sql'] = 'LOWER(tag0)=LOWER(?)';
                $ret['params'][0] = $in_value;
            }
        }
        
        if ($ret['sql']) {
            $ret['sql'] = '('.$ret['sql'].')';
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
            
            foreach ($in_value as $value) {                
                if ($value) {
                    if ($ret['sql']) {
                        $ret['sql'] .= ') OR ';
                    }
                    
                    $ret['sql'] .= '('.strval($in_db_key).'=?';
                    array_push($ret['params'], $value);
                }
            }
            
            if ($ret['sql']) {
                $ret['sql'] .= ')';
            }
        } else {
            $ret['sql'] = ''.strval($in_db_key).'=?';
            array_push($ret['params'], $in_value);
        }
        
        if ($ret['sql']) {
            $ret['sql'] = '('.$ret['sql'].')';
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
            $use_like = FALSE;
            
            if (isset($in_value['use_like'])) {
                $use_like = TRUE;
                unset($in_value['use_like']);
            }
            
            $in_value = array_unique(array_map(function($in){return strtolower(trim(strval($in)));}, $in_value));    // Make sure we don't have repeats.
            foreach ($in_value as $value) {                
                if ($value) {
                    if ($ret['sql']) {
                        $ret['sql'] .= ') OR ';
                    }
                    
                    $ret['sql'] .= '(LOWER('.strval($in_db_key).')'.($use_like ? ' LIKE ' : '=').'LOWER(?)';
                    array_push($ret['params'], $value);
                }
            }
            
            if ($ret['sql']) {
                $ret['sql'] .= ')';
            }
        } else {
            $ret['sql'] = 'LOWER('.strval($in_db_key).')=LOWER(?)';
            $ret['params'][0] = $in_value;
        }
        
        if ($ret['sql']) {
            $ret['sql'] = '('.$ret['sql'].')';
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
                                            $or_search = FALSE              ///< If TRUE, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                        ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        if (isset($in_search_parameters) && is_array($in_search_parameters) && count ($in_search_parameters)) {
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
                    if ($ret['sql']) {
                        $ret['sql'] .= ') '.($or_search ? 'OR' : 'AND').' ';
                    }
                    
                    $ret['sql'] .= '('.$temp['sql'];
                    $ret['params'] = array_merge($ret['params'], $temp['params']);
                }
            }
            
            if ($ret['sql']) {
                $ret['sql'] .= ')';
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
                                            $or_search = FALSE,             ///< If TRUE, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                            $page_size = 0,                 ///< If specified with a 1-based integer, this denotes the size of a "page" of results. NOTE: This is only applicable to MySQL or Postgres, and will be ignored if the DB is not MySQL or Postgres.
                                            $initial_page = 0,              ///< This is ignored unless $page_size is greater than 0. If so, then this 0-based index will specify which page of results to return.
                                            $and_writeable = FALSE,         ///< If TRUE, then we only want records we can modify.
                                            $count_only = FALSE,            ///< If TRUE (default is FALSE), then only a single integer will be returned, with the count of items that fit the search.
                                            $ids_only = FALSE               ///< If TRUE (default is FALSE), then the return array will consist only of integers (the object IDs). If $count_only is TRUE, this is ignored.
                                        ) {
        $ret = Array('sql' => '', 'params' => Array());
        
        $closure = '';
        $location_search = FALSE;
        
        // If we are doing a location/radius search, the predicate is a lot more complicated.
        if (isset($in_search_parameters['location']) && isset($in_search_parameters['location']['longitude']) && isset($in_search_parameters['location']['latitude']) && isset($in_search_parameters['location']['radius'])) {
            // We expand the radius by 5%, because we'll be triaging the results with the more accurate Vincenty calculation afterwards.
            $predicate_temp = $this->_location_predicate($in_search_parameters['location']['longitude'], $in_search_parameters['location']['latitude'], floatval($in_search_parameters['location']['radius']) * 1.02, $and_writeable, $count_only);
            $sql = $predicate_temp['sql'];
            $ret['params'] = $predicate_temp['params'];
            $closure = ') ORDER BY distance,id';
            $location_search = TRUE;
        } else {
            $predicate = $this->_create_security_predicate($and_writeable);
        
            if (!$predicate) {
                $predicate = 'true'; // If we are in "God Mode," we could get no predicate, so we just go with "1".
            }
        
            $sql = $count_only ? 'SELECT COUNT(*) FROM (' : '';
            $sql .= 'SELECT * FROM '.$this->table_name.' WHERE ('.$predicate;
            $closure = ') ORDER BY id';
        }
        
        if (isset($in_search_parameters) && is_array($in_search_parameters) && count($in_search_parameters)) {
            $temp_sql = '';
            $temp_params = Array();
        
            $param_ret = $this->_parse_parameters($in_search_parameters, $or_search);
            
            if ($param_ret['sql'] && count($param_ret['params'])) {
                if ($temp_sql) {
                    $temp_sql .= ') '.($or_search ? 'OR' : 'AND').' ';
                }
                $temp_sql .= $param_ret['sql'];
                $temp_params = array_merge($temp_params, $param_ret['params']);
            }
            
            if ($temp_sql) {
                $sql .= ' AND ('.$temp_sql.')';
                $ret['params'] = array_merge($ret['params'], $temp_params);
            }
        }
        
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
        } elseif ($ids_only && !$location_search) {
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
                                                                        - 'location'
                                                                            This requires that the parameter be a 3-element associative array of floating-point numbers:
                                                                                - 'longtude'
                                                                                    This is the search center location longitude, in degrees.
                                                                                - 'latitude'
                                                                                    This is the search center location latitude, in degrees.
                                                                                - 'radius'
                                                                                    This is the search radius, in Kilometers.
                                                                    */
                                    $or_search = FALSE,             ///< If TRUE, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                    $page_size = 0,                 ///< If specified with a 1-based integer, this denotes the size of a "page" of results. NOTE: This is only applicable to MySQL or Postgres, and will be ignored if the DB is not MySQL or Postgres.
                                    $initial_page = 0,              ///< This is ignored unless $page_size is greater than 0. If so, then this 0-based index will specify which page of results to return.
                                    $and_writeable = FALSE,         ///< If TRUE, then we only want records we can modify.
                                    $count_only = FALSE,            ///< If TRUE (default is FALSE), then only a single integer will be returned, with the count of items that fit the search.
                                    $ids_only = FALSE               ///< If TRUE (default is FALSE), then the return array will consist only of integers (the object IDs). If $count_only is TRUE, this is ignored.
                                    ) {
        $ret = NULL;
        
        $location_count = $count_only;
        $location_ids_only = $ids_only;
        $location_search = (isset($in_search_parameters['location']) && isset($in_search_parameters['location']['longitude']) && isset($in_search_parameters['location']['latitude']) && isset($in_search_parameters['location']['radius']));
        
        if ($location_search) { // We're forced to use the regular search for count-only location, as we need that Vincenty filter.
            $count_only = FALSE;
            $ids_only = FALSE;
        }
        
        $sql_and_params = $this->_build_sql_query($in_search_parameters, $or_search, $page_size, $initial_page, $and_writeable, $count_only, $ids_only);
        $sql = $sql_and_params['sql'];
        $params = $sql_and_params['params'];
        
        if ($sql) {
            $temp = $this->execute_query($sql, $params);
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
                            $accurate_distance = self::get_accurate_distance(floatval($in_search_parameters['location']['latitude']), floatval($in_search_parameters['location']['longitude']), floatval($item->latitude()), floatval($item->longitude()));
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
