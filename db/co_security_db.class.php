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
defined( 'LGV_SD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/**
 */
class CO_Security_DB extends A_CO_DB {
	public function __construct(    $in_pdo_object,
        	                        $in_access_object = NULL
                                ) {
        parent::__construct($in_pdo_object, $in_access_object);
        
        $this->table_name = 'co_security_nodes';
        
        $this->class_description = 'The security database class.';
    }
    
    public function get_initial_record_by_id(  $in_id
                                            ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `id`='.intval($in_id);

        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $this->instantiate_record($temp[0]);
        }
        
        return $ret;
    }
    
    public function get_initial_record_by_login_id(  $in_login_id
                                                    ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `login_id`=?';

        $temp = $this->execute_query($sql, Array($in_login_id));
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $this->instantiate_record($temp[0]);
        }
        
        return $ret;
    }
    
    public function get_single_record_by_login_id(  $in_login_id,
                                                    $in_access_ids,
                                                    $and_write = FALSE
                                                    ) {
        $ret = NULL;
        
        $temp = $this->get_multiple_records_by_login_id(Array($in_login_id), $in_access_ids, $and_write);
        
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $temp[0];
        }
        
        return $ret;
    }
    
    public function get_multiple_records_by_login_id(   $in_login_id_array,
                                                        $in_access_ids,
                                                        $and_write = FALSE
                                                    ) {
        $ret = NULL;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$this->_create_security_predicate($in_access_ids, $and_write). ' AND (';
        $params = Array();
        
        foreach ($in_login_id_array as $id) {
            if (0 < $id) {
                if (0 < count($params)) {
                    $sql .= ') OR (';
                }
                $sql.= '`login_id`=?';
                array_push($params, $id);
            }
        }
        
        $sql  .= ')';

        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = Array();
            foreach ($temp as $result) {
                array_push($ret, $this->instantiate_record($result));
            }
            usort($ret, function($a, $b){return ($a->id() > $b->id());});
        }
        
        return $ret;
    }
};
