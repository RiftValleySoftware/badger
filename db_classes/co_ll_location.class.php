<?php
/**
*/
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

require_once(CO_Config::db_class_dir().'/co_main_db_record.class.php');

/**
 */
class CO_LL_Location extends CO_Main_DB_Record {
    var $longitude;
    var $latitude;
    
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
        
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->longitude, $this->latitude)" : "($this->longitude, $this->latitude)";
        }
        
        return $ret;
    }
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        parent::__construct($in_db_object, $in_db_result);
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
