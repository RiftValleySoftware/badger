<?php
/**
*/
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

require_once(CO_Config::db_class_dir().'/a_co_main_db_record.class.php');

/**
 */
class CO_LL_Location extends A_CO_Main_DB_Record {
    var $longitude;
    var $latitude;
    
	public function __construct(    $in_db_object,
	                                $in_db_result,
	                                $in_security_id_array = null
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_security_id_array);
        
        if ($this->db_object) {
            if (isset($in_db_result['longitude'])) {
                $this->longitude = doubleval($in_db_result['longitude']);
            }
        
            if (isset($in_db_result['latitude'])) {
                $this->latitude = doubleval($in_db_result['latitude']);
            }
        }
    }
};
