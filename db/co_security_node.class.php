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
defined( 'LGV_SDBN_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/***************************************************************************************************************************/
/**
This is the base class for records in the security database.
 */
class CO_Security_Node extends A_CO_DB_Table_Base {
    protected $_ids;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is called to populate the object fields for this class with default values. These use the SQL table tags.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    This method overloads (and calls) the base class method.
    
    \returns An associative array, simulating a database read.
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['ids'] = (NULL != $this->_ids) ? $this->_ids : '';
        return $default_setup;
    }
    
    /***********************/
    /**
    This builds up the basic section of the instance database record. It should be overloaded, and the parent called before adding new fields.
    
    This method overloads (and calls) the base class method.
    
    \returns an associative array, in database record form.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ids_as_string_array = Array();
        $ids_as_int = array_map('intval', $this->_ids);
        sort($ids_as_int);
        
        foreach ($this->_ids as $id) {
            if ($id != $this->id()) {
                array_push($ids_as_string_array, strval($id));
            }
        }
        
        $id_list_string = implode(',', $ids_as_string_array);
        
        $ret['ids'] = $id_list_string;
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Initializer
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL,   ///< This is a database-format associative array that is used to initialize this instance.
	                                $in_ids = NULL          ///< This is a preset array of integers, containing security IDs for the row.
                                ) {
                                
        $this->_ids = $in_ids;
        
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
                
        if ($this->_db_object) {
            $this->_ids = Array($this->id());
            if (isset($in_db_result['ids'])) {
                $temp = $in_db_result['ids'];
                
                if (isset ($temp) && $temp) {
                    $tempAr = explode(',', $temp);
                    if (is_array($tempAr) && count($tempAr)) {
                        $tempAr = array_map('intval', $tempAr);
                        $tempAr = array_merge($this->_ids, $tempAr);
                        if (isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                            sort($tempAr);
                            $this->_ids = $tempAr;
                        }
                    }
                }
            }
        }
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Security Node ($this->_id)";
    }
    
    /***********************/
    /**
    This is a setter for the ID array. It can delete the array by sending in NULL, or an empty array.
    No user can set IDs for which they do not have access.
    Since this is a "whole hog" operation, we need to be able to access every single ID in the current object before we can replace or delete them.
    
    \returns TRUE, if successful.
     */
    public function set_ids(    $in_ids_array   ///< This is a preset array of integers, containing security IDs for the row. NULL/Empty to delete all IDs.
                            ) {
        $ret = FALSE;
        
        if ($this->user_can_edit_ids()) {
            $id_pool = $this->get_access_object()->get_security_ids();
            if ($this->get_access_object()->god_mode() || (isset($id_pool) && is_array($id_pool) && count($id_pool))) {
                // First thing we do, is ensure that EVERY SINGLE ID in the current user are ones we have in our own set.
                // An empty set is fine.
                foreach($this->_ids as $id) {
                    if (!$this->get_access_object()->god_mode() && (isset($id) && (0 < $id) && !in_array($id, $id_pool))) {
                        // Even one failure scrags the operation.
                        $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                                        CO_Lang::$db_error_name_user_not_authorized,
                                                        CO_Lang::$db_error_desc_user_not_authorized,
                                                        __LINE__,
                                                        __FILE__,
                                                        __METHOD__
                                                    );
                        return FALSE;
                    }
                }
                
                // Next, if there is an existing array, we check our input, and add only the IDs we own.
                if ($this->get_access_object()->god_mode() || (isset($in_ids_array) && is_array($in_ids_array) && count($in_ids_array))) {
                    $temp_ids = array_map('intval', $in_ids_array);
                    $new_ids = Array();
                    foreach($temp_ids as $in_id) {
                        if ($this->get_access_object()->god_mode() || (in_array($in_id, $id_pool))) {
                            $new_ids[] = $in_id;
                        }
                    }
                
                    if (count($new_ids)) {
                        $this->_ids = $new_ids;
                    }
                // Otherwise, we are clearing the array.
                } else {
                    $this->_ids = Array();
                }
        
                $ret = $this->update_db();
            } else {
                $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                                CO_Lang::$db_error_name_user_not_authorized,
                                                CO_Lang::$db_error_desc_user_not_authorized,
                                                __LINE__,
                                                __FILE__,
                                                __METHOD__
                                            );
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                            CO_Lang::$db_error_name_user_not_authorized,
                                            CO_Lang::$db_error_desc_user_not_authorized,
                                            __LINE__,
                                            __FILE__,
                                            __METHOD__
                                        );
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a setter, allowing you to add an ID.
    
    \returns TRUE, if successful.
     */
    public function add_id( $in_id  ///< A single integer. The new ID to add.
                            ) {
        $ret = FALSE;
        
        if ($this->user_can_edit_ids()) {
            $id_pool = $this->get_access_object()->get_security_ids();
            
            if ($this->get_access_object()->god_mode() || (isset($id_pool) && is_array($id_pool) && count($id_pool))) {
                // We can add an ID to the user, as long as it is one we own. We don't have to have full access to all user IDs.
                if ($this->get_access_object()->god_mode() || (in_array($in_id, $id_pool))) {
                    if (!isset($this->_ids) || !is_array($this->_ids) || !count($this->_ids)) {
                        $this->_ids = Array(intval($in_id));
                    } else {
                        $this->_ids[] = $in_id;
                        $this->_ids = array_unique($this->_ids);
                    }
        
                    $ret = $this->update_db();
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                                    CO_Lang::$db_error_name_user_not_authorized,
                                                    CO_Lang::$db_error_desc_user_not_authorized,
                                                    __LINE__,
                                                    __FILE__,
                                                    __METHOD__
                                                );
                }
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                            CO_Lang::$db_error_name_user_not_authorized,
                                            CO_Lang::$db_error_desc_user_not_authorized,
                                            __LINE__,
                                            __FILE__,
                                            __METHOD__
                                        );
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This allows you to remove a single ID.
    We can remove one of our IDs from a user that may have other IDs.
    
    \returns TRUE, if successful.
     */
    public function remove_id(  $in_id  ///< A single integer. The ID to remove.
                            ) {
        $ret = FALSE;
        
        if ($this->user_can_edit_ids()) {
            $id_pool = $this->get_access_object()->get_security_ids();
            
            if ($this->get_access_object()->god_mode() || (isset($id_pool) && is_array($id_pool) && count($id_pool) && in_array($in_id, $id_pool))) {
                if (isset($this->_ids) && is_array($this->_ids) && count($this->_ids) && $this->user_can_edit_ids()) {
                    $new_array = Array();
            
                    foreach($this->_ids as $id) {
                        if ($id != $in_id) {
                            array_push($new_array, $id);
                        } else {
                            $ret = TRUE;
                        }
                
                        if ($ret) {
                            $ret = $this->set_ids($new_array);
                        }
                    }
                }
            } else {
                $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                                CO_Lang::$db_error_name_user_not_authorized,
                                                CO_Lang::$db_error_desc_user_not_authorized,
                                                __LINE__,
                                                __FILE__,
                                                __METHOD__
                                            );
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                            CO_Lang::$db_error_name_user_not_authorized,
                                            CO_Lang::$db_error_desc_user_not_authorized,
                                            __LINE__,
                                            __FILE__,
                                            __METHOD__
                                        );
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns The current IDs.
     */
    public function ids() {
        return $this->_ids;
    }
    
    /***********************/
    /**
    This is designed to be overloaded. By default, only God can change IDs.
    
    \returns TRUE, if the current logged-in user can edit IDs for this login.
     */
    public function user_can_edit_ids() {
        return $this->get_access_object()->god_mode();
    }
};
