<?php
/**
*/
defined( 'LGV_ADB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined( 'LGV_DBF_CATCHER' ) ) {
    define( 'LGV_DBF_CATCHER', 1 );
}
	    
if ( !defined('LGV_ERROR_CATCHER') ) {
    define('LGV_ERROR_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_main_db_record.class.php');

require_once(CO_Config::shared_class_dir().'/error.class.php');

/**
 */
abstract class A_CO_DB {
    var $pdo_object;
    var $class_description;
    var $error;
    var $table_name;
    
    function instantiate_record(  $in_db_result
                                            ) {
        $ret = null;
        
        $classname = trim($in_db_result['access_class']);
        
        if ($classname) {
            if (!class_exists($classname)) {
                $filename = CO_Config::db_classes_class_dir().'/'.strtolower($classname).'.class.php';
                require_once($filename);
            }
            
            if (class_exists($classname)) {
                $ret = new $classname($this, $in_db_result);
            }
        }
        
        return $ret;
    }

	public function __construct(    $in_pdo_object
                                ) {
        $this->class_description = 'Abstract Base Class for Database -Should never be instantiated.';
        
        $this->error = null;
        $this->table_name = null;
        $this->pdo_object = $in_pdo_object;
    }
    
    public function execute_query(  $in_sql,
                                    $in_parameters = null
                                    ) {
        $ret = null;
        $this->error = null;
        
        $ret = $this->pdo_object->preparedQuery($in_sql, $in_parameters, false);
        
        return $ret;
    }
    
    public function get_single_record_by_id(    $in_id
                                            ) {
        $ret = null;
        
        $temp = $this->get_multiple_records_by_id(Array($in_id));
        
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $temp[0];
        }
        
        return $ret;
    }
    
    public function get_multiple_records_by_id( $in_id_array
                                                ) {
        $ret = null;
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE (';
        $params = Array();
        
        $id_array = array_map(function($in){ return intval($in); }, $in_id_array);
        
        foreach ($id_array as $id) {
            if (0 < $id) {
                if (0 < count($params)) {
                    $sql .= ') OR (';
                }
                $sql.= '`id`=?';
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
        }
        
        return $ret;
    }
};
