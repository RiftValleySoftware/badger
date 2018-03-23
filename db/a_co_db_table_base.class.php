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
defined( 'LGV_ADBTB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.
        
if ( !defined('LGV_DBF_CATCHER') ) {
    define('LGV_DBF_CATCHER', 1);
}

require_once(CO_Config::db_classes_class_dir().'/co_security_login.class.php');

/***************************************************************************************************************************/
/**
This is the abstract base class for All database records used by Badger.

Badger works on a very simple basis. All records, regardless of their ultimate implementation, have the same basic structure.

The data and security database tables each take a slightly different tack from the baseline, but this class describes the baseline,
which is common to both tabases and tables.
 */
abstract class A_CO_DB_Table_Base {
    protected   $_db_object;    ///< This is the actual database object that "owns" this instance. It should not be exposed beyond this class or subclasses, thereof.
    protected   $_id;           ///< This is the within-table unique ID of this record.
    
    var $class_description;     ///< This is a description of the class (not the instance).
    var $instance_description;  ///< This is a description that describes the instance.
    
    var $last_access;           ///< This is a UNIX epoch date that describes the last modification. The default is UNIX Day Two (in case of UTC timezone issues).
    var $name;                  ///< This is the "object_name" string field.
    var $read_security_id;      ///< This is a single integer, defining the security ID required to view the record. If it is 0, then it is "open."
    var $write_security_id;     ///< This is a single integer, defining the required security token to modify the record. If it is 0, then any logged-in user can modify.
    var $context;               ///< This is a mixed associative array, containing fields for the object.
    var $error;                 ///< If there is an error, it is contained here, in a LGV_Error instance.
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is called to populate the object fields for this class with default values. These use the SQL table tags.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    \returns An associative array, simulating a database read.
     */
    protected function _default_setup() {
        return Array(   'id'                    => 0,
                        'last_access'           => 86400,   // Default is first UNIX day.
                        'object_name'           => '',
                        'read_security_id'      => 0,
                        'write_security_id'     => 0,
                        'access_class_context'  => NULL
                        );
    }
    
    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    \returns TRUE, if the instance was able to set itself up to the provided array.
     */
    protected function _load_from_db($in_db_result  ///< This is an associative array, formatted as a database row response.
                                    ) {
        $ret = FALSE;
        $this->last_access = max(86400, time());    // Just in case of badly-set clocks in the server.
        
        if (isset($this->_db_object) && isset($in_db_result) && isset($in_db_result['id']) && intval($in_db_result['id'])) {
            $ret = TRUE;
            $this->_id = intval($in_db_result['id']);
            
            if (isset($in_db_result['last_access'])) {
                $date_from_db = date_create_from_format('Y-m-d H:i:s', $in_db_result['last_access']);
                $timestamp = date_timestamp_get($date_from_db);
                $this->last_access = max(86400, $timestamp);
            }
        
            if (isset($in_db_result['read_security_id'])) {
                $this->read_security_id = intval($in_db_result['read_security_id']);
            }
        
            if (isset($in_db_result['write_security_id'])) {
                $this->write_security_id = intval($in_db_result['write_security_id']);
            } else {
                $this->write_security_id = $this->read_security_id ? -1 : 0;  // Writing is completely blocked if we have read security, but no write security specified.
            }
            
            if (isset($in_db_result['object_name'])) {
                $this->name = strval($in_db_result['object_name']);
            }
            
            if (isset($in_db_result['access_class_context'])) {
                $temp_context = unserialize($in_db_result['access_class_context']);
    
                if ($temp_context) {
                    $this->context = $temp_context;
                }
            }
        }
        
        $this->class_description = 'Abstract Base Class for Records -Should never be instantiated.';

        return $ret;
    }
    
    /***********************/
    /**
    This builds up the basic section of the instance database record. It should be overloaded, and the parent called before adding new fields.
    
    \returns an associative array, in database record form.
     */
    protected function _build_parameter_array() {
        $ret = Array();
        
        $ret['id'] = $this->id();
        $ret['access_class'] = get_class($this);
        $ret['last_access'] = date('Y-m-d H:i:s');
        $ret['read_security_id'] = $this->read_security_id;
        $ret['write_security_id'] = $this->write_security_id;
        $ret['object_name'] = $this->name;
        $ret['access_class_context'] = $this->context ? serialize($this->context) : NULL;   // If we have a context, then we serialize it for the DB.
        
        return $ret;
    }
    
    /***********************/
    /**
    This is the fundamental database write function. It's a "trigger," and the state of the instance is what is written. If the 'id' field is 0, then a new databse row is created.
    
    This calls the _build_parameter_array() function to create a database-format associative array that is interpreted into SQL by the "owning" database object.
    
    \returns FALSE if there was an error. Otherwise, it returns the ID of the element just written (or created).
     */
    protected function _write_to_db() {
        $ret = FALSE;
        
        if (isset($this->_db_object)) {
            $params = $this->_build_parameter_array();
            
            if (isset($params) && is_array($params) && count($params)) {
                $ret = $this->_db_object->write_record($params);
                $this->error = $this->_db_object->access_object->error;
                if ((1 < intval($ret)) && !$this->error) {
                    $this->_id = intval($ret);
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This function deletes this object's record from the database.
    
    It should be noted that a successful seppuku means the instance is no longer viable, and the ID is set to 0, which makes it a new record (if saved).
     */
    protected function _seppuku() {
        $ret = FALSE;
        
        if ($this->id() && isset($this->_db_object)) {
            $ret = $this->_db_object->delete_record($this->id());
            if ($ret) {
                $this->_id = 0; // Make sure we have no more ID. If anyone wants to re-use this instance, it needs to become a new record.
            }
        }
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is the basic constructor.
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL    ///< This is a database-format associative array that is used to initialize this instance.
                                ) {
        $this->class_description = '';
        $this->_id = NULL;
        $this->last_access = time();
        $this->read_security_id = 0;
        $this->write_security_id = 0;
        $this->name = NULL;
        $this->context = NULL;
        $this->instance_description = NULL;
        $this->_db_object = $in_db_object;
        $this->error = NULL;
        
        if ($in_db_object) {
            if (!$in_db_result) {
                $in_db_result = $this->_default_setup();
            }
        
            $this->_load_from_db($in_db_result);
        }
    }
    
    /***********************/
    /**
    Simple accessor for the ID.
    
    \returns the integer ID of the instance.
     */
    public function id() {
        return $this->_id;
    }
    
    /***********************/
    /**
    \returns TRUE, if the current logged-in user has write permission on this record.
     */
    public function user_can_write() {
        $ret = FALSE;
        
        $login_item = $this->_db_object->access_object->get_login_item();
        
        $my_write_item = intval($this->write_security_id);
        
        if (isset($login_item) && $login_item instanceof CO_Security_Login) {
            if ((0 == $my_write_item) || ($login_item->id() == CO_Config::$god_mode_id)) {
                $ret = TRUE;
            } else {
                $ids = $login_item->ids;
                foreach ($ids as $id) { 
                    if (intval($id) == $my_write_item) {
                        $ret = TRUE;
                        break;
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter Accessor for the Read Security ID. Also updates the DB.
    
    This checks to make sure the user has write permission before changing the ID.
    
    \returns TRUE, if a DB update was successful.
     */
    public function set_read_security_id($in_new_id ///< The new value
                                        ) {
        $ret = FALSE;
        
        if ($this->user_can_write() && isset($in_new_id)) {
            $this->read_security_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter Accessor for the Write Security ID. Also updates the DB.
    
    This checks to make sure the user has write permission before changing the ID.
    
    \returns TRUE, if a DB update was successful.
     */
    public function set_write_security_id($in_new_id    ///< The new value
                                        ) {
        $ret = FALSE;
        if ($this->user_can_write() && isset($in_new_id)) {
            $this->write_security_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter Accessor for the Object Name. Also updates the DB.
    
    \returns TRUE, if a DB update was successful.
     */
    public function set_name($in_new_value  ///< The new value
                            ) {
        $ret = FALSE;
        
        if (isset($in_new_value)) {
            $this->name = strval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is the public database record deleter.
    
    This checks to make sure the user has write permission before deleting.
    
    \returns TRUE, if the deletion was successful.
     */
    public function delete_from_db() {
        if ($this->user_can_write()) {
            return $this->_seppuku();
        } else {
            return FALSE;
        }
    }
    
    /***********************/
    /**
    This is a "trigger" to update the database with the current instance state.
    
    This checks to make sure the user has write permission before saving.
    
    \returns TRUE, if a DB update was successful.
     */
    public function update_db() {
        if ($this->user_can_write()) {
            if ( $this->_write_to_db() ) {
                return $this->reload_from_db(); // Make sure that we get exactly what we wrote.
            }
        } else {
            return FALSE;
        }
    }
    
    /***********************/
    /**
     */
    public function reload_from_db() {
        $db_result = $this->_db_object->get_single_raw_row_by_id($this->id());
        $this->error = $this->_db_object->access_object->error;
        return $this->_load_from_db($db_result);
    }
};
