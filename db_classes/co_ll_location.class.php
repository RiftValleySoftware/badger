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
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        
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
};
