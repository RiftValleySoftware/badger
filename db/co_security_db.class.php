<?php
/**
*/
defined( 'LGV_SD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/**
 */
class CO_Security_DB extends A_CO_DB {
	public function __construct(    $in_pdo_object
                                ) {
        parent::__construct($in_pdo_object);
        
        $this->table_name = 'co_security_nodes';
        
        $this->class_description = 'The security database class.';
    }
    
    public function get_single_record_by_login_id(  $in_login_id
                                                ) {
        $ret = null;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `login_id`=?';
        $params = Array($in_login_id);
        
        $temp = $this->execute_query($sql, $params);
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $this->instantiate_record($temp[0]);
        }
        
        return $ret;
    }
};
