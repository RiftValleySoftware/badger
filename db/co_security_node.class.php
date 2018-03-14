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
    var $ids;
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
                
        $this->ids = NULL;
        
        if ($this->db_object) {
            $this->ids = Array($this->id);
            if (isset($in_db_result['ids'])) {
                $temp = $in_db_result['ids'];
                
                if (isset ($temp) && $temp) {
                    $tempAr = explode(',', $temp);
                    if (is_array($tempAr) && count($tempAr)) {
                        $tempAr = array_map(function($in) { return intval($in); }, $tempAr);
                        $tempAr = array_merge($this->ids, $tempAr);
                        if ( isset($tempAr) && is_array($tempAr) && count($tempAr)) {
                            sort($tempAr);
                            $this->ids = $tempAr;
                        }
                    }
                }
            }
        }
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->id)" : "Unnamed Security Node ($this->id)";
    }
};
