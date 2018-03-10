<?php
/**
*/
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/**
 */
class A_CO_Main_DB_Record extends A_CO_DB_Table_Base {
    var $owner_id;
    var $name;
    var $tags;
    
    private $raw_payload;
    
	public function __construct(    $in_db_object,
	                                $in_db_result,
	                                $in_security_id_array = null
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_security_id_array);
        
        if ($this->db_object) {
            $this->owner_id = null;
            $this->name = null;
            $this->tags = array();
            $this->raw_payload = null;
        
            if (isset($in_db_result['owner_id'])) {
                $this->owner_id = $in_db_result['owner_id'];
            }
    
            if (isset($in_db_result['name'])) {
                $this->name = $in_db_result['name'];
            }

            if (isset($in_db_result['payload']) ) {
                $this->raw_payload = $in_db_result['payload'];
            }
        
            for ($i = 0; $i < 10; $i++) {
                $tagname = 'tag'.$i;
                $this->tags[$i] = null;
                if (isset($in_db_result[$tagname])) {
                    $this->tags[$i] = $in_db_result[$tagname];
                }
            }
        }
    }
    
    public function payload(    $in_private_key = null
                            ) {
        if ($in_private_key) {
            $ret = $this->decrypt_payload_with_private_key($in_private_key);
        } else {
            $ret = $this->raw_payload;
        }
        
        return $ret;
    }
    
    public function decrypt_payload_with_private_key(   $in_private_key
                                                    ) {
        $ret = null;
        
        if (!openssl_private_decrypt($this->raw_payload, $ret, $in_private_key)) {
            $ret = null;
        }
        
        return $ret;
    }
};
