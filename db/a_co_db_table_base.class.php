<?php
/**
*/
defined( 'LGV_ADBTB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
 */
abstract class A_CO_DB_Table_Base {
    var $db_object;
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
	                                $in_db_result,
	                                $in_security_id_array = null
                                ) {
        $this->class_description = 'Abstract Base Class for Records -Should never be instantiated.';
        $this->id = null;
        $this->last_access = null;
        $this->read_security_id = null;
        $this->write_security_id = null;
        $this->ttl = null;
        $this->name = null;
        $this->context = null;
        $this->instance_description = null;
        
        // We're not even allowed to open the door if there's read security, and we're not authorized.
        if (    !isset($in_db_result['read_security_id'])   // Null read security means everyone can read.
            ||  (null == $in_db_result['read_security_id'])
            ||  (   isset($in_security_id_array) // Otherwise, make sure that one of the IDs in the passed-in array is the read security ID.
                    && is_array($in_security_id_array)
                    && in_array($in_db_result['read_security_id'], $in_security_id_array, TRUE)
                )
            ) {
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
                    $this->write_security_id = $this->read_security_id ? -1 : null;  // Writing is completely blocked if we have read security, but no write security specified.
                }
                
                if (isset($in_db_result['ttl'])) {
                    $this->ttl = $in_db_result['ttl'];
                }
                
                if (isset($in_db_result['name'])) {
                    $this->name = $in_db_result['name'];
                }
                
                if (isset($in_db_result['access_class_context'])) {
                    $temp_context = unserialize($in_context);
        
                    if ($temp_context) {
                        $this->context = $temp_context;
                    }
                }
            }
        }
    }
    
    public function is_allowed_to_write($in_security_id_array = null
                                        ) {
        $ret = FALSE;
        
        // Make sure that we're allowed to write before we start.
        if (    $this->db_object
            &&  !(  isset($this->write_security_id) // First, see if we're completely blocked.
                    && (-1 == $this->write_security_id))
            &&  (   !isset($this->write_security_id)    // If not, do we have write security set?
                ||  (null == $this->write_security_id)
                ||  (   isset($in_security_id_array)    // If so, are we on the guest list?
                        && is_array($in_security_id_array)
                        && in_array($this->write_security_id, $in_security_id_array, TRUE)
                    )
                )
            ) {
            $ret = TRUE;
        }
        
        return $ret;
    }
    
    public function construct_prepared_statement($in_security_id_array = null
                                                ) {
    }
};
