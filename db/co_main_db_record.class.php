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
class CO_Main_DB_Record extends A_CO_DB_Table_Base {
    static  $s_table_name = 'co_data_nodes';

    var $owner_id;
    var $tags;
    
    private $raw_payload;

    protected function _load_from_db($in_db_result) {
        $ret = parent::_load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'Base Class for Main Database Records.';
            $this->name = (isset($this->name) && trim($this->name)) ? trim($this->name) : "Base Class Instance ($this->id)";
            
            if ($this->db_object) {
                $this->owner_id = NULL;
                $this->tags = array();
                $this->raw_payload = NULL;
        
                if (isset($in_db_result['owner_id'])) {
                    $this->owner_id = $in_db_result['owner_id'];
                }

                if (isset($in_db_result['payload']) ) {
                    $this->raw_payload = $in_db_result['payload'];
                }
        
                for ($i = 0; $i < 10; $i++) {
                    $tagname = 'tag'.$i;
                    $this->tags[$i] = NULL;
                    if (isset($in_db_result[$tagname])) {
                        $this->tags[$i] = $in_db_result[$tagname];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['owner_id'] = $this->owner_id;
        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            $ret[$key] = $this->tags[$tag_no];
        }
        
        $ret['payload'] = $this->get_storage_payload();
        
        return $ret;
    }
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        parent::__construct($in_db_object, $in_db_result);
    }
    
    public function get_payload() {
        $ret = $this->get_decrypted_payload();
        
        if (!$ret) {
            $ret = $this->raw_payload;
        }
        
        return $ret;
    }
    
    public function get_storage_payload() {
        $ret = $this->get_encrypted_payload();
        
        if (!$ret) {
            $ret = $this->raw_payload;
        }
        
        return $ret;
    }
    
    public function get_decrypted_payload() {
        $private_key = $this->access_object->get_login_item()->get_private_key();
        
        $ret = NULL;

        if ($private_key) {
            $ret = $this->decrypt_payload_with_private_key($private_key);
        }
        
        return $ret;
    }
    
    public function get_encrypted_payload() {
        $private_key = $this->access_object->get_login_item()->get_private_key();
        
        $ret = NULL;

        if ($private_key) {
            $ret = $this->encrypt_payload_with_private_key($private_key);
        }
        
        return $ret;
    }
    
    public function encrypt_payload_with_private_key(   $in_private_key
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_encrypt($this->raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    public function decrypt_payload_with_private_key(   $in_private_key
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_decrypt($this->raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    public function reload_from_db() {
        $db_result = $this->$db_object->access_object->get_single_data_record_by_id($this->id);
        return $this->_load_from_db($db_result);
    }
};
