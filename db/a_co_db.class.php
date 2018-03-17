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

require_once(CO_Config::db_class_dir().'/co_main_db_record.class.php');

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
                                    $in_parameters = NULL,
                                    $exec_only = FALSE
                                    ) {
        $ret = NULL;
        $this->error = NULL;
        
        try {
            if ($exec_only) {
                $this->pdo_object->preparedExec($in_sql, $in_parameters);
            } else {
                $ret = $this->pdo_object->preparedQuery($in_sql, $in_parameters, FALSE);
            }
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_security_db,
                                            CO_Lang::$pdo_error_name_failed_to_open_security_db,
                                            CO_Lang::$pdo_error_desc_failed_to_open_security_db,
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
        $ret = FALSE;
        
        if (isset($params_associative_array) && is_array($params_associative_array) && count($params_associative_array)) {
            if (isset($in_access_ids) && is_array($in_access_ids) && count($in_access_ids)) {
                $predicate = $this->_create_security_predicate($in_access_ids, TRUE);
            
                $id = isset($params_associative_array['id']) ? intval($params_associative_array['id']) : 0;  // We extract  the ID from the fields, or assume a new record.
                
                if (0 < $id) {
                    // First, we look for a record with our ID, and for which we have write permission.
                    $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE '.$predicate.' AND `id`='.$id;

                    $temp = $this->execute_query($sql, Array());
                
                    if (isset($temp) && $temp && is_array($temp) && (1 == count($temp)) ) { // If we  got a record, then we'll be updating it.
                        $sql = 'UPDATE `'.$this->table_name.'`';
                        unset($params_associative_array['id']); // We remove the ID parameter. That can't be changed.
                        
                        // Make sure that we aren't changing the security ID to one we can't read.
                        if (isset($params_associative_array['read_security_id'])) {
                            $new_read_id = intval($params_associative_array['read_security_id']);
                            if ($new_read_id && !in_array($new_read_id, $in_access_ids)) {
                                unset($params_associative_array['read_security_id']);
                            }
                        }
                        
                        // Make sure that we aren't changing the security ID to one we can't write.
                        if (isset($params_associative_array['write_security_id'])) {
                            $new_write_id = intval($params_associative_array['write_security_id']);
                            if ($new_write_id && !in_array($new_write_id, $in_access_ids)) {
                                unset($params_associative_array['write_security_id']);
                            }
                        }
                        
                        $params = array_values($params_associative_array);
                        $keys = array_keys($params_associative_array);
                        $set_sql = '`'.implode('`=?,`', $keys).'`=?';
                    
                        $sql .= ' SET '.$set_sql.' WHERE ('.$predicate.' AND `id`='.$id.')';
                        $this->execute_query($sql, $params, TRUE);
                        
                        if (!$this->error) {
                            $ret = TRUE;
                        }
                    } else {
                        $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `id`='.$id; // Look for a record with the proposed ID (assume we don't have permission).

                        $temp = $this->execute_query($sql, Array());
                        
                        if (isset($temp) && $temp && is_array($temp) && count($temp)) { // If we  got a record, then we're aborting, as we tried to change an existing rcord.
                            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_write_attempt,
                                                            CO_Lang::$pdo_error_name_illegal_write_attempt,
                                                            CO_Lang::$pdo_error_desc_illegal_write_attempt);
                        }
                    }
                } else {
                    $sql = 'SELECT * FROM `'.$this->table_name.'` WHERE `id`=1'; // We simply get the template.

                    $temp = $this->execute_query($sql, Array());
                    
                    if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                        $sql = 'INSERT INTO `'.$this->table_name.'`';
                        unset($params_associative_array['id']);
                        
                        // Make sure that we aren't setting the security ID to one we can't read.
                        if (isset($params_associative_array['read_security_id'])) {
                            $new_read_id = intval($params_associative_array['read_security_id']);
                            if ($new_read_id && !in_array($new_read_id, $in_access_ids)) {
                                unset($params_associative_array['read_security_id']);
                            }
                        }
                        
                        // Make sure that we aren't setting the security ID to one we can't write.
                        if (isset($params_associative_array['write_security_id'])) {
                            $new_write_id = intval($params_associative_array['write_security_id']);
                            if ($new_write_id && !in_array($new_write_id, $in_access_ids)) {
                                unset($params_associative_array['write_security_id']);
                            }
                        }
                        
                        // If there is no read ID specified, it becomes public.
                        if (!isset($params_associative_array['read_security_id'])) {
                            $params_associative_array['read_security_id'] = 0;
                        }
                        
                        // If there is no write ID specified, then we simply take the first one off the list.
                        if (!isset($params_associative_array['write_security_id'])) {
                            $params_associative_array['write_security_id'] = $in_access_ids[1];
                        }
                        
                        foreach ($params_associative_array as $key => $value) {
                            if (isset($temp[$key])) {
                                $temp[$key] = $value;
                            }
                        }
                        
                        $keys = array_keys($params_associative_array);
                    
                        $keys_sql = '(`'.implode('`,`', $keys).'`)';
                        $values_sql = '('.implode(',', array_map(function($in){return '?';}, $keys)).')';
                        
                        $sql .= " $keys_sql VALUES $values_sql";
                        $this->execute_query($sql, array_values($params_associative_array), TRUE);
                        
                        $sql = 'SELECT LAST_INSERT_ID() FROM `'.$this->table_name.'`';
                        $id_ar = $this->execute_query($sql, array_values($params_associative_array));

                        if (!$this->error && isset($id_ar) && is_array($id_ar) && count($id_ar)) {
                            // Get the ID of the new row we just created.
                            $ret = $id_ar[0]['last_insert_id()'];
                        }
                    } else {
                        $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_write_attempt,
                                                        CO_Lang::$pdo_error_name_illegal_write_attempt,
                                                        CO_Lang::$pdo_error_desc_illegal_write_attempt);
                    }
                }
            }
        }
                
        return $ret;
    }
    
    public function delete_record(  $id,
                                    $in_access_ids
                                ) {
        $id = intval($id);
        $predicate = $this->_create_security_predicate($in_access_ids, TRUE);
        // First, make sure we have write permission for this record, and that the record exists.
        $sql = 'SELECT id FROM `'.$this->table_name.'` WHERE ('.$predicate.' AND `id`='.$id.')';
        $temp = $this->execute_query($sql);
        
        if (!$this->error && isset($temp) && $temp && is_array($temp) && (1 == count($temp))) {
            $sql = 'DELETE FROM `'.$this->table_name.'` WHERE `id`='.$id;
            $this->execute_query($sql, Array(), TRUE);
            if ($this->error) {
                $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_delete_attempt,
                                                CO_Lang::$pdo_error_name_failed_delete_attempt,
                                                CO_Lang::$pdo_error_desc_failed_delete_attempt);
            } else {
                // Make sure she's dead, Jim.
                $sql = 'SELECT id FROM `'.$this->table_name.'` WHERE ('.$predicate.' AND `id`='.$id.')';
                $temp = $this->execute_query($sql);
        
                if (!$this->error && isset($temp) && $temp && is_array($temp) && (0 == count($temp))) {
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_delete_attempt,
                                                    CO_Lang::$pdo_error_name_failed_delete_attempt,
                                                    CO_Lang::$pdo_error_desc_failed_delete_attempt);
                }
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_delete_attempt,
                                            CO_Lang::$pdo_error_name_illegal_delete_attempt,
                                            CO_Lang::$pdo_error_desc_illegal_delete_attempt);
        }
    }
};
