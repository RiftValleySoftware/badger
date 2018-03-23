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
 */
class CO_Security_Node extends A_CO_DB_Table_Base {
    var $ids;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['ids'] = (NULL != $this->ids) ? $this->ids : '';
        return $default_setup;
    }
    
    /***********************/
    /**
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ids_as_string_array = array_map(function($in) { return strval($in); }, $this->ids);
        
        $id_list_string = implode(',', ids_as_string_array);
        
        $ret['ids'] = $id_list_string;
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
	public function __construct(    $in_db_object = NULL,
	                                $in_db_result = NULL,
	                                $in_ids = NULL
                                ) {
                                
        $this->ids = $in_ids;
        
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
                
        if ($this->_db_object) {
            $this->ids = Array($this->id());
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
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Security Node ($this->_id)";
    }
    
    /***********************/
    /**
     */
    public function set_ids($in_ids_array
                            ) {
        $ret = FALSE;
        
        if (isset($in_ids_array) && is_array($in_ids_array) && count($in_ids_array)) {
            $this->ids = array_map(function($in) { return intval($in); }, $in_ids_array);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function add_id( $in_id
                            ) {
        $ret = FALSE;
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function remove_id( $in_id
                            ) {
        $ret = FALSE;
        
        return $ret;
    }
};
