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
        
        $sql = 'SELECT ids FROM '.$this->table_name.' WHERE (login_id IS NOT NULL) AND (id='.intval($in_id).')';

        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = explode(',', $temp[0]['ids']);
            if (isset($ret) && is_array($ret) && count($ret)) {
                array_unshift($ret, $in_id);
                $ret = array_unique(array_map('intval', $ret));
                $ret_temp = Array();
                foreach ( $ret as $i) {
                    if (0 < $i) {
                        array_push($ret_temp, $i);
                    }
                }
                $ret = $ret_temp;
            }
        } else {
            $ret = Array($in_id);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a very "raw" function that should ONLY be called from the access instance __construct() method (or a special check from the access class).
    
    It is designed to fetch the current login object from its string login ID, so we can extract the id.
    
    It has no security screening, as it needs to be called before the security screens can be put into place.
    
    \returns a newly-instantiated record.
     */
    public function get_initial_record_by_login_id( $in_login_id    ///< The login ID of the element.
                                                    ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE login_id=?';
        $params = Array(trim($in_login_id));
        
        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $result = $this->_instantiate_record($temp[0]);
            if ($result) {
                $ret = $result;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a special "raw" function for getting login credentials from an API key.
    
    \returns an associative array, with the login ID and hashed password that correspond to the API key.
     */
    public function get_credentials_by_api_key( $in_api_key    ///< The API Key of the element.
                                                ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE api_key=?';
        $params = Array(trim($in_api_key));
        
        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $result = $this->_instantiate_record($temp[0]);
            if ($result) {
                $ret = Array('login_id' => $result->login_id, 'hashed_password' => $result->get_crypted_password());
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a very "raw" function that should ONLY be called from a special check from the access class.
    
    It has no security screening, as this is just for checking.
    
    \returns a newly-instantiated record.
     */
    public function get_initial_record_by_id( $in_id    ///< The ID of the element.
                                            ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE id='.intval($in_id);
        
        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $result = $this->_instantiate_record($temp[0]);
            if ($result) {
                $ret = $result;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a security-screened method to fetch a single instance of a record object, based on its ID.
    
    \returns a single, newly-instantiated object.
     */
    public function get_single_record_by_login_id(  $in_login_id,       ///< The login ID of the requested login object.
                                                    $and_write = false  ///< If this is true, then we need the item to be modifiable.
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
                                                        $and_write = false
                                                    ) {
        $ret = NULL;
        
        $predicate = $this->_create_security_predicate($and_write);
        
        if ($predicate) {   // If we got a predicate, then we AND it with the rest of the statement.
            $predicate .= ' AND ';
        }
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate. '(';
        
        $params = Array();
        
        foreach ($in_login_id_array as $id) {
            if (trim($id)) {                
                if (0 < count($params)) {
                    $sql .= ') OR (';
                }
                $sql.= 'login_id=?';
                array_push($params, $id);
            }
        }
        
        $sql  .= ')';
        
        if (0 < count($params)) {
            $temp = $this->execute_query($sql, $params);
            if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                $ret = Array();
                foreach ($temp as $result) {
                    $result = $this->_instantiate_record($result);
                    if ($result) {
                        array_push($ret, $result);
                    }
                }
                usort($ret, function($a, $b){return ($a->id() > $b->id());});
            }
        }
        
        // At this point, only managers (or the login object, itself) can see logins. God, too, of course.
        $my_id = $this->access_object->get_login_id();
        $my_login_instance_is_manager = $this->access_object->god_mode() || ($this->access_object->get_login_item() instanceof CO_Login_Manager);
        $temp_ret = Array();
        
        if (isset($ret) && is_array($ret) && count($ret)) {
            foreach ($ret as $instance) {
                if (($instance->id() == $my_id) || $my_login_instance_is_manager) {
                    array_push($temp_ret, $instance);
                }
            }
        }
        
        return $temp_ret;
    }
    
    /***********************/
    /**
    This is a security-vetted search for all login objects (visible to the current user).
    
    \returns an array of instances.
     */
    public function get_all_login_objects ( $and_write = false  ///< If true, then we only want ones we have write access to.
                                            ) {
        $ret = Array();
        
        // Can only look for tokens we can see.
        
        $predicate = $this->_create_security_predicate($and_write);
        
        if ($predicate) {   // If we got a predicate, then we AND it with the rest of the statement.
            $predicate .= ' AND ';
        }
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate. '(login_id IS NOT NULL) AND (login_id<>\'\')';
        $temp = $this->execute_query($sql, Array());    // We just get everything.
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            foreach ($temp as $result) {
                $result = $this->_instantiate_record($result);
                if ($result) {
                    array_push($ret, $result);
                }
            }
        }
        
        // At this point, only managers (or the login object, itself) can see logins. God, too, of course.
        $my_id = $this->access_object->get_login_id();
        $my_login_instance_is_manager = $this->access_object->god_mode() || ($this->access_object->get_login_item() instanceof CO_Login_Manager);
        $temp_ret = Array();
        
        foreach ($ret as $instance) {
            if (($instance->id() == $my_id) || $my_login_instance_is_manager) {
                array_push($temp_ret, $instance);
            }
        }
        
        return $temp_ret;
    }
    
    /***********************/
    /**
    You give a security ID, and you will get all login objects that have that token in their list (or are of that ID).
    
    This is restricted to use security vetting, so only logins visible to the current login.
       
    \returns an array of instances.
     */
    public function get_all_login_objects_with_access(  $in_security_token, ///< An integer, with the requested security token.
                                                        $and_write = false  ///< If true, then we only want ones we have write access to.
                                                        ) {
        $ret = Array();
        
        $in_security_token = intval($in_security_token);
        
        $access_ids = $this->access_object->get_security_ids();
        
        // Can only look for tokens we can see.
        if (($this->access_object->god_mode() && $in_security_token) || in_array($in_security_token, $access_ids)) {
            $predicate = $this->_create_security_predicate($and_write);
        
            if ($predicate) {   // If we got a predicate, then we AND it with the rest of the statement.
                $predicate .= ' AND ';
            }
            
            $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate.'(login_id IS NOT NULL) AND (login_id<>\'\')';
            $temp = $this->execute_query($sql, Array());    // We just get everything.
            if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                $ret = Array();
                
                foreach ($temp as $result) {
                    $id = intval($result['id']);
                    $id_array = Array($id);
                    $ids = explode(',', $result['ids']);
                    if (isset($ids) && is_array($ids) && count($ids)) {
                        $ids = array_map('trim', $ids);
                        $ids = array_map('intval', $ids);
                        
                        foreach ($ids as $single_id) {
                            array_push($id_array, $single_id);
                        }
                    }
                    
                    $found = false;
                    foreach ($id_array as $test_id) {
                        if ($test_id == $in_security_token) {
                            $found = true;
                            break;
                        }
                    }
                    
                    if ($found) {
                        $result = $this->_instantiate_record($result);
                        if ($result) {
                            array_push($ret, $result);
                        }
                    }
                }
            }
        }
        
        // At this point, only managers (or the login object, itself) can see logins. God, too, of course.
        $my_id = $this->access_object->get_login_id();
        $my_login_instance_is_manager = $this->access_object->god_mode() || ($this->access_object->get_login_item() instanceof CO_Login_Manager);
        $temp_ret = Array();
        
        foreach ($ret as $instance) {
            if (($instance->id() == $my_id) || $my_login_instance_is_manager) {
                array_push($temp_ret, $instance);
            }
        }
        
        return $temp_ret;
    }
};
