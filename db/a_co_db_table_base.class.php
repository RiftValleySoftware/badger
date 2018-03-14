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
    
        if (isset($in_db_object) && isset($in_db_result) && isset($in_db_result['id']) && intval($in_db_result['id'])) {
            $this->id = intval($in_db_result['id']);
        
            if (isset($in_db_result['last_access'])) {
                $this->last_access = $in_db_result['last_access'];
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
                $this->ttl = $in_db_result['ttl'];
            }
            
            if (isset($in_db_result['name'])) {
                $this->name = $in_db_result['name'];
            }
            
            if (isset($in_db_result['access_class_context'])) {
                $temp_context = unserialize($in_db_result['access_class_context']);
    
                if ($temp_context) {
                    $this->context = $temp_context;
                }
            }
        }
    }
};
