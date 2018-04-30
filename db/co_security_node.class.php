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
        
        $ids_as_string_array = array_map('strval', $this->_ids);
        
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
    This is a setter for the ID array.
    
    \returns TRUE, if successful.
     */
    public function set_ids(    $in_ids_array   ///< This is a preset array of integers, containing security IDs for the row.
                            ) {
        $ret = FALSE;
        
        if (isset($in_ids_array) && is_array($in_ids_array) && count($in_ids_array) && $this->user_can_edit_ids()) {
            $this->_ids = array_map('intval', $in_ids_array);
            $ret = $this->update_db();
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
        
        if (!isset($this->_ids) || !is_array($this->_ids) && $this->user_can_edit_ids()) {
            $this->_ids = Array(intval($in_id));
        } else {
            $this->_ids[] = $in_id;
            $this->_ids = array_unique($this->_ids);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This allows you to remove a single ID.
    
    \returns TRUE, if successful.
     */
    public function remove_id(  $in_id  ///< A single integer. The ID to remove.
                            ) {
        $ret = FALSE;
        
        if (isset($this->_ids) && is_array($this->_ids) && count($this->_ids) && $this->user_can_edit_ids()) {
            $new_array = Array();
            
            foreach($this->_ids as $id) {
                if ($id != $in_id) {
                    array_push($new_array, $id);
                }
                
                $ret = $this->set_ids($new_array);
            }
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
    Users cannot edit their own IDs, no matter who they are (unless they are God).
    
    \returns TRUE, if the current logged-in user can edit IDs for this login.
     */
    public function user_can_edit_ids() {
        $ret - $this->get_access_object()->god_mode();
        
        if (!$ret && $this->user_can_write()) { // Can't write, then we don't even bother going further.
            $ids = $this->get_access_object()->get_security_ids();
            
            $valid_ids = array_filter($ids, function($in_id){return intval($in_id) && (intval($in_id) != $this->id);});
            
            $ret = in_array($this->get_access_object()->get_login_id(), $valid_ids);
        }
        
        return $ret;
    }
};
