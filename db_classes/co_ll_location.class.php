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

require_once(CO_Config::db_class_dir().'/co_main_db_record.class.php');

/***************************************************************************************************************************/
/**
This is a specialization of the basic data class, implementing the long/lat fields (built into the table structure, but unused by base classes).
 */
class CO_LL_Location extends CO_Main_DB_Record {
    protected $_longitude;
    protected $_latitude;
    
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
        $default_setup['longitude'] = (NULL != $this->_longitude) ? $this->_longitude : 0;
        $default_setup['latitude'] = (NULL != $this->_latitude) ? $this->_latitude : 0;
        
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
        
        $ret['longitude'] = $this->_longitude;
        $ret['latitude'] = $this->_latitude;
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Constructor (Initializer)
     */
	public function __construct(    $in_db_object = NULL,   ///< The database object for this instance.
	                                $in_db_result = NULL,   ///< The database row for this instance (associative array, with database keys).
	                                $in_owner_id = NULL,    ///< The ID of the object (in the database) that "owns" this instance.
	                                $in_tags_array = NULL,  ///< An array of strings, up to ten elements long, for the tags.      
	                                $in_longitude = NULL,   ///< An initial longitude value.
	                                $in_latitude = NULL,    ///< An initial latitude value.
	                                $in_fuzz_factor = NULL  ///< An initial "fuzz factor" value.
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_owner_id, $in_tags_array);
        
        if (NULL != $in_longitude) {
            $this->_longitude = $in_longitude;
        }
        
        if (NULL != $in_latitude) {
            $this->_latitude = $in_latitude;
        }
        
        if (NULL != $in_fuzz_factor) {
            $this->context['fuzz_factor'] = $in_fuzz_factor;
        } elseif (!isset($this->context['fuzz_factor']) || !$this->context['fuzz_factor']) {
            $this->context['fuzz_factor'] = 0;
        }
            
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns TRUE, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db(   $in_db_result   ///< This is an associative array, formatted as a database row response.
                                    ) {
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'A basic class for long/lat locations.';
        
            if ($this->_db_object) {
                if (isset($in_db_result['longitude'])) {
                    $this->_longitude = doubleval($in_db_result['longitude']);
                }
        
                if (isset($in_db_result['latitude'])) {
                    $this->_latitude = doubleval($in_db_result['latitude']);
                }
            }
        }
        
        $this->class_description = "Generic longitude/latitude Class.";
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_longitude, $this->_latitude)" : "($this->_longitude, $this->_latitude)";
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a "trigger" to update the database with the current instance state.
    
    This checks to make sure the user has write permission before saving.
    
    \returns TRUE, if a DB update was successful.
     */
    public function update_db() {
        $ret = parent::update_db();
        if ($ret) {
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_longitude, $this->_latitude)" : "($this->_longitude, $this->_latitude)";
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter for longitude.
    
    \returns TRUE, if the save was successful.
     */
    public function set_longitude(  $in_new_value
                                    ) {
        $ret = FALSE;
        
        if (isset($in_new_value) && $this->user_can_write()) {
            $this->_longitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter for latitude.
    
    \returns TRUE, if the save was successful.
     */
    public function set_latitude(   $in_new_value
                                    ) {
        $ret = FALSE;
        
        if (isset($in_new_value) && $this->user_can_write()) {
            $this->_latitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Getter for fuzz factor.
    
    \returns the fuzz factor, as a float. If it is not set, then it is zero.
     */
     public function fuzz_factor() {
        return abs(isset($this->context['fuzz_factor']) ? floatval($this->context['fuzz_factor']) : 0);
    }
    
    /***********************/
    /**
    Setter for fuzz factor.
    
    \returns TRUE, if the save was successful.
     */
    public function set_fuzz_factor(   $in_new_value    ///< The new value must be a positive floating-point value over (and including) 0. If zero, the factor is deleted.
                                    ) {
        $ret = FALSE;
        
        $in_new_value = abs(floatval($in_new_value));
        
        if ($this->user_can_write()) {
            if (0 == $in_new_value) {
                unset($this->context['fuzz_factor']);
            } else {
                $this->context['fuzz_factor'] = $in_new_value;
            }
            
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This returns the longitude. However, if the user is not logged in, or doesn't have read rights (which shouldn't happen, anyway), they will only get the "fuzzed" version.
    
    \returns The current longitude value.
     */
    public function raw_longitude() {
        if ($this->get_access_object()->security_db_available() && $this->user_can_read()) {
            return $this->_longitude;
        } else {
            return $this->longitude();
        }
    }
    
    /***********************/
    /**
    This returns the latitude. However, if the user is not logged in, or doesn't have read rights (which shouldn't happen, anyway), they will only get the "fuzzed" version.
    
    \returns The current longitude value.
     */
    public function raw_latitude() {
        if ($this->get_access_object()->security_db_available() && $this->user_can_read()) {
            return $this->_latitude;
        } else {
            return $this->latitude();
        }
    }
    
    /***********************/
    /**
    This returns the longitude, with any "fuzz factor" applied.
    
    \returns The current longitude value.
     */
    public function longitude() {
        return $this->_longitude * (0 < $this->fuzz_factor()) ? (random_int(1, max(1, 100000 * ($this->fuzz_factor() + 1))) / 100000.0) : 1.0;
    }
    
    /***********************/
    /**
    This returns the longitude, with any "fuzz factor" applied.
    
    \returns The current latitude value.
     */
    public function latitude() {
        return $this->_latitude * (0 < $this->fuzz_factor()) ? (random_int(1, max(1, 100000 * ($this->fuzz_factor() + 1))) / 100000.0) : 1.0;
    }
};
