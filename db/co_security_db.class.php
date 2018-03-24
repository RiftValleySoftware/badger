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
defined( 'LGV_SD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/***************************************************************************************************************************/
/**
This is the base class for the security database.
It assumes that it will have logins and Security ID table rows.
 */
class CO_Security_DB extends A_CO_DB {
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is the initializer.
     */
	public function __construct(    $in_pdo_object,             ///< The PDO instance for this database.
        	                        $in_access_object = NULL    ///< The access object (if any) for this login.
                                ) {
        parent::__construct($in_pdo_object, $in_access_object);
        
        $this->table_name = 'co_security_nodes';    // This is the name of the SQL table we will query.
        
        $this->class_description = 'The security database class.';  // A simple explanation of what this class is.
    }
    
    /***********************/
    /**
    This returns just the security IDs (including the item ID, itself) for the given ID.
    
    This should only be called from the ID fetcher in the access class, as it does not do a security predicate.
    
    \returns an array of integers, each, a security ID for the given login, and the first element is always the login ID itself.
     */
    public function get_security_ids_for_id(    $in_id  ///< The integer ID of the row.
                                            ) {
        $ret = NULL;
        
        $sql = 'SELECT ids FROM `'.$this->table_name.'` WHERE `id`='.intval($in_id);

        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = explode(',', $temp[0]['ids']);
            if (isset($ret) && is_array($ret)) {
                array_unshift($ret, $in_id);
                $ret = array_unique($ret);
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a very "raw" function that should ONLY be called from the access instance __construct() method.
    
    It is designed to fetch the current login object from its string login ID, so we can extract the id.
    
    It has no security screening, as it needs to be called before the security screens can be put into place.
    
    \returns a newly-instantiated record.
     */
    public function get_initial_record_by_login_id( $in_login_id    ///< The login ID of the element.
                                                    ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `login_id`=?';

        $temp = $this->execute_query($sql, Array($in_login_id));
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $this->_instantiate_record($temp[0]);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a security-screened method to fetch a single instance of a record object, based on its ID.
    
    \returns a single, newly-instantiated object.
     */
    public function get_single_record_by_login_id(  $in_login_id,       ///< The login ID of the requested login object.
                                                    $and_write = FALSE  ///< If this is TRUE, then we need the item to be modifiable.
                                                    ) {
        $ret = NULL;
        
        $temp = $this->get_multiple_records_by_login_id(Array($in_login_id), $and_write);
        
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $temp[0];
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a security-screened multiple login fetcher.
    
    \returns an array of newly-instantiated objects.
     */
    public function get_multiple_records_by_login_id(   $in_login_id_array,
                                                        $and_write = FALSE
                                                    ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$this->_create_security_predicate($and_write). ' AND (';
        $params = Array();
        
        foreach ($in_login_id_array as $id) {
            if (0 < $id) {                
                if (0 < count($params)) {
                    $sql .= ') OR (';
                }
                $sql.= '`login_id`=?';
                array_push($params, $id);
            }
        }
        
        $sql  .= ')';

        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = Array();
            foreach ($temp as $result) {
                array_push($ret, $this->_instantiate_record($result));
            }
            usort($ret, function($a, $b){return ($a->id() > $b->id());});
        }
        
        return $ret;
    }
};
