<?php
/**
*/
defined( 'LGV_ADBTB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
 */
abstract class A_CO_DB_Table_Base {
    protected  $db_object;
    var $class_description;
    var $instance_description;
    
    var $id;
    var $last_access;
    var $ttl;
    var $name;
    var $read_security_id;
    var $write_security_id;
    var $context;
    var $error;
    
    protected function _load_from_db($in_db_result) {
        $ret = FALSE;
        
        if (isset($this->db_object) && isset($in_db_result) && isset($in_db_result['id']) && intval($in_db_result['id'])) {
            $ret = TRUE;
            $this->id = intval($in_db_result['id']);
            
            if (isset($in_db_result['last_access'])) {
                $this->last_access = strtotime($in_db_result['last_access']);
            }
        
            if (isset($in_db_result['read_security_id'])) {
                $this->read_security_id = intval($in_db_result['read_security_id']);
            }
        
            if (isset($in_db_result['write_security_id'])) {
                $this->write_security_id = intval($in_db_result['write_security_id']);
            } else {
                $this->write_security_id = $this->read_security_id ? -1 : 0;  // Writing is completely blocked if we have read security, but no write security specified.
            }
            
            if (isset($in_db_result['ttl'])) {
                $this->ttl = intval($in_db_result['ttl']);
            }
            
            if (isset($in_db_result['object_name'])) {
                $this->name = strval($in_db_result['object_name']);
            }
            
            if (isset($in_db_result['access_class_context'])) {
                $temp_context = unserialize($in_db_result['access_class_context']);
    
                if ($temp_context) {
                    $this->context = $temp_context;
                }
            }
        }
        
        return $ret;
    }
    
    protected function _write_to_db() {
        $ret = FALSE;
        
        if (isset($this->db_object)) {
            $params = $this->_build_parameter_array();
            
            if (isset($params) && is_array($params) && count($params)) {
                $ret = $this->db_object->write_record($params);
                $this->error = $this->db_object->access_object->error;
            }
        }
        
        return $ret;
    }
    
    protected function _build_parameter_array() {
        $ret = Array();
        
        $ret['id'] = $this->id;
        $ret['access_class'] = get_class($this);
        $ret['last_access'] = NULL;
        $ret['read_security_id'] = $this->read_security_id;
        $ret['write_security_id'] = $this->write_security_id;
        $ret['ttl'] = $this->ttl;
        $ret['object_name'] = $this->name;
        $ret['access_class_context'] = $this->context ? serialize($this->context) : NULL;
        
        return $ret;
    }
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        $this->class_description = 'Abstract Base Class for Records -Should never be instantiated.';
        $this->id = NULL;
        $this->last_access = NULL;
        $this->read_security_id = 0;
        $this->write_security_id = 0;
        $this->ttl = NULL;
        $this->name = NULL;
        $this->context = NULL;
        $this->instance_description = NULL;
        $this->db_object = $in_db_object;
        $this->error = NULL;
    
        $this->_load_from_db($in_db_result);
    }
    
    public function seconds_remaining_to_live() {
        $interval = NULL;
        
        if (isset($this->last_access) && (NULL != $this->last_access) && (NULL != $this->ttl) && intval($this->ttl)) {
            $time = time();
            $interval = intval($this->ttl) - (intval($time) - intval($this->last_access));
        }
        
        return $interval;
    }
    
    public function set_read_security_id($in_new_id
                                        ) {
        $ret = FALSE;
        
        if (isset($in_new_id)) {
            $this->read_security_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    public function set_write_security_id($in_new_id
                                        ) {
        $ret = FALSE;
        
        if (isset($in_new_id)) {
            $this->write_security_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    public function set_ttl($in_new_value
                                        ) {
        $ret = FALSE;
        
        if (isset($in_new_value)) {
            $this->ttl = intval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    public function set_name($in_new_value
                                        ) {
        $ret = FALSE;
        
        if (isset($in_new_value)) {
            $this->name = strval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    public function past_sell_by_date() {
        return ((NULL != $this->seconds_remaining_to_live()) && (NULL != $this->ttl) && intval($this->ttl)) ? intval($this->seconds_remaining_to_live()) > intval($this->ttl) : FALSE;
    }
    
    public function update_db() {
        return $this->_write_to_db();
    }
    
    public function reload_from_db() {
        return FALSE;
    }
};
