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

/**
 */
class CO_LL_Location extends CO_Main_DB_Record {
    var $longitude;
    var $latitude;
    
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['longitude'] = (NULL != $this->longitude) ? $this->longitude : 0;
        $default_setup['latitude'] = (NULL != $this->latitude) ? $this->latitude : 0;
        
        return $default_setup;
    }
    
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['longitude'] = $this->longitude;
        $ret['latitude'] = $this->latitude;
        
        return $ret;
    }

    protected function _load_from_db($in_db_result) {
        $ret = parent::_load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'A basic class for long/lat locations.';
        
            if ($this->db_object) {
                if (isset($in_db_result['longitude'])) {
                    $this->longitude = doubleval($in_db_result['longitude']);
                }
        
                if (isset($in_db_result['latitude'])) {
                    $this->latitude = doubleval($in_db_result['latitude']);
                }
            }
        }
        
        $this->class_description = "Generic longitude/latitude Class.";
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->longitude, $this->latitude)" : "($this->longitude, $this->latitude)";
        
        return $ret;
    }
    
	public function __construct(    $in_db_object = NULL,
	                                $in_db_result = NULL,
	                                $in_longitude = NULL,
	                                $in_latitude = NULL,
	                                $in_owner_id = NULL,
	                                $in_tags = NULL
                                ) {
        $this->longitude = $in_longitude;
        $this->latitude = $in_latitude;
        
        parent::__construct($in_db_object, $in_db_result, $in_owner_id, $in_tags);
    }
    
    public function update_db() {
        $ret = parent::update_db();
        if ($ret) {
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->longitude, $this->latitude)" : "($this->longitude, $this->latitude)";
        }
        
        return $ret;
    }
    
    public function set_longitude(  $in_new_value
                                    ) {
        $ret = FALSE;
        
        if (isset($in_new_value)) {
            $this->longitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    public function set_latitude(   $in_new_value
                                    ) {
        $ret = FALSE;
        
        if (isset($in_new_value)) {
            $this->latitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
};
