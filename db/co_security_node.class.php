<?php
/**
*/
defined( 'LGV_SDBN_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/**
 */
class CO_Security_Node extends A_CO_DB_Table_Base {
    static  $s_table_name = 'co_security_nodes';
    
    var $ids;
    
	public function __construct(    $in_db_object,
	                                $in_db_result,
	                                $in_security_id_array = null
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_security_id_array);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
                
        $this->ids = null;
        
        if ($this->db_object) {
            $this->ids = Array($this->id);
            if (isset($in_db_result['ids'])) {
                $temp = $in_db_result['ids'];
                
                if (isset ($temp) && $temp) {
                    $tempAr = explode(',', $temp);
                    if (is_array($tempAr) && count($tempAr)) {
                        private function mapper($in) { return intval($in); }
                        $tempAr = array_map($tempAr, mapper);
                        $this->ids = array_merge($this->ids, $tempAr);
                    }
                }
            }
        }
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->id)" : "Unnamed Security Node ($this->id)";
    }
};
