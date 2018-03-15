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
    
    protected function _create_read_security_predicate(  $in_access_ids
                                                    ) {
        if (1 == count($in_access_ids) && (-1 == intval($in_access_ids[0]))) {
            return '1';
        }
        
        $ret = '((`read_security_id`=0) OR (`read_security_id` IS NULL)';
        
        if (isset($in_access_ids) && is_array($in_access_ids) && count($in_access_ids)) {
            foreach ($in_access_ids as $access_id) {
                $ret .= ' OR (`read_security_id`='.intval($access_id).')';
            }
        }
        
        $ret .= ')';
        
        return $ret;
    }
    
    protected function _create_write_security_predicate(  $in_access_ids
                                                    ) {
        if (1 == count($in_access_ids) && (-1 == intval($in_access_ids[0]))) {
            return '1';
        }
        
        $ret = '((`write_security_id`=0) OR (`write_security_id` IS NULL)';
        
        if (isset($in_access_ids) && is_array($in_access_ids) && count($in_access_ids)) {
            foreach ($in_access_ids as $access_id) {
                $ret .= ' OR (`write_security_id`='.intval($access_id).')';
            }
        }
        
        $ret .= ')';
        
        return $ret;
    }
    
    protected function _create_security_predicate(  $in_access_ids,
                                                    $and_write = FALSE
                                                    ) {
        if (1 == count($in_access_ids) && (-1 == intval($in_access_ids[0]))) {
            return '1';
        }
        
        $ret = $this->_create_read_security_predicate($in_access_ids);
        
        if ($and_write) {
            $ret .= ' AND '.$this->_create_write_security_predicate($in_access_ids);
        }
        
        return $ret;
    }
    
    public function instantiate_record(  $in_db_result
                                            ) {
        $ret = NULL;
        
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
        
        $this->error = NULL;
        $this->table_name = NULL;
        $this->pdo_object = $in_pdo_object;
    }
    
    public function execute_query(  $in_sql,
                                    $in_parameters = NULL
                                    ) {
        $ret = NULL;
        $this->error = NULL;
        
        try {
            $ret = $this->pdo_object->preparedQuery($in_sql, $in_parameters, FALSE);
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   CO_Access::$pdo_error_code_failed_to_open_security_db,
                                            CO_Access::$pdo_error_name_failed_to_open_security_db,
                                            CO_Access::$pdo_error_desc_failed_to_open_security_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            $this->_security_db_object = NULL;
        }
        
        return $ret;
    }
    
    public function get_single_record_by_id(    $in_id,
                                                $in_access_ids = NULL,
                                                $and_write = FALSE
                                            ) {
        $ret = NULL;
        
        $temp = $this->get_multiple_records_by_id(Array($in_id), $in_access_ids, $and_write);
        
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $temp[0];
        }
        
        return $ret;
    }
    
    public function get_multiple_records_by_id( $in_id_array,
                                                $in_access_ids = NULL,
                                                $and_write = FALSE
                                                ) {
        $ret = NULL;
        
        $predicate = $this->_create_security_predicate($in_access_ids, $and_write);
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$predicate. ' AND (';
        $params = Array();
        $id_array = array_map(function($in){ return intval($in); }, $in_id_array);
        
        foreach ($id_array as $id) {
            if (0 < $id) {
                if (0 < count($params)) {
                    $sql .= ' OR ';
                }
                $sql.= '(`id`=?)';
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
            usort($ret, function($a, $b){return ($a->id > $b->id);});
        }
        
        return $ret;
    }
    
    public function get_all_readable_records(   $in_access_ids = NULL
                                            ) {
        $ret = NULL;
        
        $predicate = $this->_create_security_predicate($in_access_ids);
        
        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$predicate;

        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = Array();
            foreach ($temp as $result) {
                array_push($ret, $this->instantiate_record($result));
            }
            usort($ret, function($a, $b){return ($a->id > $b->id);});
        }
        
        return $ret;
    }
    
    public function get_all_writeable_records(  $in_access_ids = NULL
                                            ) {
        $ret = NULL;
        
        // Only logged-in users can write.
        if (isset($in_access_ids) && is_array($in_access_ids) && count($in_access_ids)) {
            $predicate = $this->_create_security_predicate($in_access_ids, TRUE);
        
            $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$predicate;

            $temp = $this->execute_query($sql, Array());
            if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                $ret = Array();
                foreach ($temp as $result) {
                    array_push($ret, $this->instantiate_record($result));
                }
                usort($ret, function($a, $b){return ($a->id > $b->id);});
            }
        }
        
        return $ret;
    }
    
    public function write_record(   $params_associative_array,
                                    $in_access_ids
                                ) {
        $ret = NULL;
        
        return $ret;
    }
    
    public function delete_record(  $id,
                                    $in_access_ids
                                ) {
        $ret = NULL;
        
        return $ret;
    }
};
