<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2021, The Great Rift Valley Software Company
    
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

    The Great Rift Valley Software Company: https://riftvalleysoftware.com
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
    protected $_personal_ids;
    
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
        $default_setup['personal_ids'] = (NULL != $this->_personal_ids) ? $this->_personal_ids : '';
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
        $personal_ids_as_string_array = Array();
        $ids_as_int = array_map('intval', $this->_ids);
        $personal_ids_as_int = array_map('intval', $this->_personal_ids);
        sort($ids_as_int);
        sort($personal_ids_as_int);
        
        foreach ($this->_ids as $id) {
            if ($id != $this->id() && !in_array($id, $personal_ids_as_int)) {
                array_push($ids_as_string_array, strval($id));
            }
        }
        
        foreach ($this->_personal_ids as $id) {
            array_push($personal_ids_as_string_array, strval($id));
        }
        
        $id_list_string = trim(implode(',', $ids_as_string_array));
        $personal_id_list_string = trim(implode(',', $personal_ids_as_string_array));
        
        $ret['ids'] = $id_list_string ? $id_list_string : NULL;
        $ret['personal_ids'] = $personal_id_list_string ? $personal_id_list_string : NULL;
        $ret['login_id'] = NULL;
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Initializer
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL,   ///< This is a database-format associative array that is used to initialize this instance.
	                                $in_ids = NULL,         ///< This is a preset array of integers, containing security IDs for the row.
	                                $in_personal_ids = NULL ///< This is a preset array of integers, containing personal security IDs for the row.
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
        
        // If explicit IDs are passed in, then that overrides the DB.
        if (isset($in_ids) && is_array($in_ids) && count($in_ids)) {
            $in_db_result['ids'] = implode(',', $in_ids);
        }
        
        if (isset($in_personal_ids) && is_array($in_personal_ids) && count($in_personal_ids)) {
            $in_db_result['personal_ids'] = implode(',', $in_ids);
        }
        
        if ($this->_db_object) {
            $this->_ids = Array($this->id());
            $this->_personal_ids = Array();
            
            if (isset($in_db_result['ids']) && $in_db_result['ids']) {
                $temp = $in_db_result['ids'];
                if (isset ($temp) && $temp) {
                    $tempAr = explode(',', $temp);
                    if (is_array($tempAr) && count($tempAr)) {
                        $tempAr = array_unique(array_map('intval', $tempAr));
                        $tempAr = array_merge($this->_ids, $tempAr);
                        sort($tempAr);
                        if (isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                            $this->_ids = $tempAr;
                        }
                    }
                }
            }
            
            if (isset($in_db_result['personal_ids']) && $in_db_result['personal_ids']) {
                $temp = $in_db_result['personal_ids'];
                if (isset ($temp) && $temp) {
                    $tempAr = explode(',', $temp);
                    if (is_array($tempAr) && count($tempAr)) {
                        $tempAr = array_unique(array_map('intval', $tempAr));
                        sort($tempAr);
                        if (isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                            $this->_personal_ids = $tempAr;
                        }
                    }
                }
            }
        }
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Security Node ($this->_id)";
    }
    
    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    \returns true, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db(   $in_db_result   ///< This is an associative array, formatted as a database row response.
                                ) {
        $this->_ids = Array($this->id());
        $this->_personal_ids = Array();
                
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret && ((isset($in_db_result['ids']) && $in_db_result['ids']) || (isset($in_db_result['personal_ids']) && $in_db_result['personal_ids']))) {
            if ($this->_db_object) {
                if (isset($in_db_result['ids']) && $in_db_result['ids']) {
                    $temp = $in_db_result['ids'];
                    if (isset ($temp) && $temp) {
                        $tempAr = explode(',', $temp);
                        if (is_array($tempAr) && count($tempAr)) {
                            $tempAr = array_map('intval', $tempAr);
                            sort($tempAr);
                            $tempAr = array_unique(array_merge($this->_ids, $tempAr));
                            if (isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                                $this->_ids = $tempAr;
                            }
                        }
                    }
                }
                  
                if (isset($in_db_result['personal_ids']) || isset($in_db_result['personal_ids'])) {
                    $temp = $in_db_result['personal_ids'];
                    if (isset ($temp) && $temp) {
                        $tempAr = explode(',', $temp);
                        if (is_array($tempAr) && count($tempAr)) {
                            $tempAr = array_unique(array_map('intval', $tempAr));
                            sort($tempAr);
                            if (isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                                $this->_personal_ids = $tempAr;
                            }
                        }
                    }
                }
            }
        }
        
        return $ret;
    }
        
    /***********************/
    /**
    This is a setter for the ID array. It can delete the array by sending in NULL, or an empty array.
    No user can set IDs for which they do not have access.
    Since this is a "whole hog" operation, we need to be able to access every single ID in the current object before we can replace or delete them.
    
    \returns true, if successful.
     */
    public function set_ids(    $in_ids_array   ///< This is a preset array of integers, containing security IDs for the row. NULL/Empty to delete all IDs.
                            ) {
        $ret = false;
        
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
                        return false;
                    }
                }
                
                // Next, if there is an existing array, we check our input, and add only the IDs we own.
                if ($this->get_access_object()->god_mode() || (isset($in_ids_array) && is_array($in_ids_array) && count($in_ids_array))) {
                    $temp_ids = array_map('intval', $in_ids_array);
                    $new_ids = Array();
                    foreach($temp_ids as $in_id) {
                        if (($in_id != $this->id()) && ($this->get_access_object()->god_mode() || in_array($in_id, $id_pool))) {
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
    
    \returns true, if successful.
     */
    public function add_id( $in_id  ///< A single integer. The new ID to add.
                            ) {
        $ret = false;
        
        if ($this->user_can_edit_ids() || ($this->_added_new_id == $in_id)) {
            $id_pool = $this->get_access_object()->get_security_ids();
            
            if ($this->get_access_object()->god_mode() || (isset($id_pool) && is_array($id_pool) && count($id_pool))) {
                // We can add an ID to the user, as long as it is one we own. We don't have to have full access to all user IDs.
                if (($this->get_access_object()->god_mode() || (in_array($in_id, $id_pool)) || ($this->_added_new_id == $in_id)) && ($in_id != $this->id())) {
                    if (!isset($this->_ids) || !is_array($this->_ids) || !count($this->_ids)) {
                        $this->_ids = Array(intval($in_id));
                    } else {
                        $this->_ids[] = $in_id;
                        $this->_ids = array_unique($this->_ids);
                    }
                    
                    $ret = $this->update_db();
                } else {
                    if ($in_id != $this->id()) {
                        $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_user_not_authorized,
                                                        CO_Lang::$db_error_name_user_not_authorized,
                                                        CO_Lang::$db_error_desc_user_not_authorized,
                                                        __LINE__,
                                                        __FILE__,
                                                        __METHOD__
                                                    );
                    } else {    // If we tried to add our own ID, then we don't add it, but it's not an error.
                        $ret = true;
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
        
        return $ret;
    }
    
    /***********************/
    /**
    This sets just the "personal" IDs for the given ID.
    
    This should only be called by the "God" admin, and will fail, otherwise (returns empty array).
    
    This is not an atomic operation. If any of the given IDs are also in the regular ID list, they will be removed from the personal IDs.
    
    \returns an array of integers, with the new personal security IDs (usually a copy of the input Array). It will be empty, if the procedure fails.
     */
    public function set_personal_ids(   $in_personal_ids = []    ///< An Array of Integers, with the new personal IDs. This replaces any previous ones. If empty, then the IDs are removed.
                                    ) {
        $ret = [];
        
        if ($this->get_access_object()->god_mode()) {
            $personal_ids_temp = array_unique($in_personal_ids);
            $personal_ids = [];
            // None of the ids can be in the regular IDs, and will be removed from the set, if so.
            // They also cannot be anyone else's personal ID, or anyone's login ID. Personal IDs can ONLY be regular (non-login) security objects.
            foreach($personal_ids_temp as $id) {
                if (!in_array($id, $this->_ids) && !$this->get_access_object()->is_this_a_login_id($id) && (!$this->get_access_object()->is_this_a_personal_id($id) || in_array($id, $this->_personal_ids))) {
                    array_push($personal_ids, $id);
                }
            }
            sort($personal_ids);
            $this->_personal_ids = $personal_ids;
            
            if ($this->update_db()) {
                return $this->_personal_ids;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This allows you to remove a single ID.
    We can remove one of our IDs from a user that may have other IDs.
    
    \returns true, if successful.
     */
    public function remove_id(  $in_id  ///< A single integer. The ID to remove.
                            ) {
        $ret = false;
        
        if ($this->user_can_edit_ids()) {
            $id_pool = $this->get_access_object()->get_security_ids();
            
            if ($this->get_access_object()->god_mode() || (isset($id_pool) && is_array($id_pool) && count($id_pool) && in_array($in_id, $id_pool))) {
                if (isset($this->_ids) && is_array($this->_ids) && count($this->_ids) && $this->user_can_edit_ids()) {
                    $new_array = Array();
            
                    foreach($this->_ids as $id) {
                        if ($id != $in_id) {
                            array_push($new_array, $id);
                        } else {
                            $ret = true;
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
    This does a security vetting. If logged in as God, then all IDs are returned. Otherwise, only IDs that our login can see are returned, whether or not they are in the object.
    
    \returns The current IDs.
     */
    public function ids() {
        if ($this->get_access_object()->god_mode()) {
            return $this->_ids;
        } else {
            $my_ids = $this->get_access_object()->get_security_ids();
            $ret = Array();
            foreach ($this->_ids as $id) {
                if (in_array($id, $my_ids)) {
                    array_push($ret, $id);
                }
            }
            return $ret;
        }
    }
    
    /***********************/
    /**
    This does a security vetting. If logged in as God, then all IDs are returned. Otherwise, only IDs that our login can see are returned, whether or not they are in the object.
    
    \returns The current personal IDs.
     */
    public function personal_ids() {
        if ($this->get_access_object()->god_mode()) {
            return $this->_personal_ids;
        } else {
            $my_ids = $this->get_access_object()->get_security_ids();
            $ret = Array();
            foreach ($this->_personal_ids as $id) {
                if (in_array($id, $my_ids)) {
                    array_push($ret, $id);
                }
            }
            return $ret;
        }
    }
    
    /***********************/
    /**
    We check to see if we can edit the IDs for this record.
    We cannot edit our own IDs, and have to own all of the IDs in the object in order to be able to change them.
    Of course, God can do whatever God wants...
    
    \returns true, if the current logged-in user can edit IDs for this login.
     */
    public function user_can_edit_ids() {
        $ret = ($this->get_access_object()->get_login_id() != $this->_id) && ($this->get_access_object()->god_mode() || $this->_db_object->i_have_all_ids($this->_id));
        
        return $ret;
    }
};
