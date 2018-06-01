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
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/***************************************************************************************************************************/
/**
This is the main instance base class for records in the main "data" database.
 */
class CO_Main_DB_Record extends A_CO_DB_Table_Base {
    static  $s_table_name = 'co_data_nodes';

    protected $_owner_id;
    protected $_tags;
    
    private $_raw_payload;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This prepares an associative array of database values for the object.
    
    \returns an associative array of default values, using the database keys.
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['owner_id'] = $this->_owner_id;

        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            $tag_val = (isset($this->_tags) && is_array($this->_tags) && ($tag_no < count($this->_tags))) ? $this->_tags[$tag_no] : '';
            $default_setup[$key] = $tag_val;
        }
        
        return $default_setup;
    }
    
    /***********************/
    /**
    This adds our data to the saved parameter associative array.
    
    \returns an associative array of instance values, using the database keys.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();    // Start with the base class.
        
        $ret['owner'] = $this->_owner_id;
        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            if (isset($this->_tags[$tag_no])) {
                $ret[$key] = $this->_tags[$tag_no];
            } else {
                $ret[$key] = '';
            }
        }
        
        $ret['payload'] = base64_encode($this->_raw_payload);
        
        return $ret;
    }

    /***********************************************************************************************************************/
    /***********************/
    /**
    The initializer.
     */
	public function __construct(    $in_db_object = NULL,   ///< The database object for this instance.
	                                $in_db_result = NULL,   ///< The database row for this instance (associative array, with database keys).
	                                $in_owner_id = NULL,    ///< The ID of the object (in the database) that "owns" this instance.
	                                $in_tags_array = NULL   ///< An array of strings, up to ten elements long, for the tags.      
                                ) {
        $this->_owner_id = intval($in_owner_id);
        $this->_tags = (isset($in_tags_array) && is_array($in_tags_array) && count($in_tags_array)) ? array_map(function($in) { return strval($in); }, $in_tags_array) : Array();
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'Base Class for Main Database Records.';
        $this->name = (isset($this->name) && trim($this->name)) ? trim($this->name) : "Base Class Instance ($this->_id)";
    }

    /***********************/
    /**
    This sets up the instance, based on a supplied associative array of database values and keys.
    
    \returns true, if sucessful.
     */
    public function load_from_db(   $in_db_result   ///< This is the associative array of database values.
                                    ) {
        $ret = parent::load_from_db($in_db_result);    // Start by calling the base class version.
        
        if ($ret) { // If that went OK, we add our own two cents...
            $this->class_description = 'Base Class for Main Database Records.';
            $this->name = (isset($this->name) && trim($this->name)) ? trim($this->name) : "Base Class Instance ($this->_id)";
            
            if ($this->_db_object) {
                $this->_owner_id = NULL;
                $this->_tags = array();
                $this->_raw_payload = NULL;
        
                if (isset($in_db_result['owner'])) {
                    $this->_owner_id = $in_db_result['owner'];
                }

                if (isset($in_db_result['payload']) ) {
                    $this->_raw_payload = base64_decode($in_db_result['payload']);
                }
                
                for ($tag_no = 0; $tag_no < 10; $tag_no++) {
                    $key = "tag$tag_no";
                    $tag_val = (isset($in_db_result[$key])) && $in_db_result[$key] ? $in_db_result[$key] : '';
                    $this->_tags[$tag_no] = $tag_val;
                }
        
                for ($i = 0; $i < 10; $i++) {
                    $tagname = 'tag'.$i;
                    $this->_tags[$i] = '';
                    if (isset($in_db_result[$tagname])) {
                        $this->_tags[$i] = $in_db_result[$tagname];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Simple setter for the owner ID.
    
    \returns true, if successful.
     */
    public function set_owner_id(   $in_new_id  ///< The new value
                                        ) {
        $ret = false;
        
        if (isset($in_new_id) && $this->user_can_write()) {
            $this->_owner_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Simple setter for the tags.
    
    \returns true, if successful.
     */
    public function set_tags(   $in_tags_array  ///< An array of strings, up to ten elements long, for the tags.
                            ) {
        $ret = false;
        
        if (isset($in_tags_array) && is_array($in_tags_array) && count($in_tags_array) && (11 > count($in_tags_array)) && $this->user_can_write()) {
            $this->_tags = array_map('strval', $in_tags_array);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter for one tag, by index.
    
    \returns true, if successful.
     */
    public function set_tag(    $in_tag_index,  ///< The index (0-based -0 through 9) of the tag to set.
                                $in_tag_value   ///< A string, with the tag value.
                            ) {
        $ret = false;
        
        $in_tag_index = intval($in_tag_index);
        
        if ((10 > $in_tag_index) && $this->user_can_write()) {
            if (!isset($this->_tags) || !$this->_tags) {
                $this->_tags = Array();
            }
            
            $this->_tags[$in_tag_index] = (NULL != $in_tag_value) ? strval($in_tag_value) : NULL;
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Returns the payload.
    
    \returns the payload, in whatever form it takes.
     */
    public function get_payload() {
        return $this->_raw_payload;
    }
    
    /***********************/
    /**
    Sets the payload.
    
    \returns true, if successful.
     */
    public function set_payload(    $in_payload ///< The raw payload to be stored.
                                ) {
        $ret = false;
        
        if ($this->user_can_write()) {
            $this->_raw_payload = $in_payload;
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns the current owner ID.
     */
    public function owner_id() {
        return $this->_owner_id;
    }
    
    /***********************/
    /**
    \returns the current tags.
     */
    public function tags() {
        return $this->_tags;
    }
};
