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
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/***************************************************************************************************************************/
/**
 */
class CO_Main_DB_Record extends A_CO_DB_Table_Base {
    static  $s_table_name = 'co_data_nodes';

    var $owner_id;
    var $tags;
    
    private $_raw_payload;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['owner_id'] = $this->owner_id;

        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            $tag_val = (isset($this->tags) && is_array($this->tags) && ($tag_no < count($this->tags))) ? $this->tags[$tag_no] : '';
            $default_setup[$key] = $tag_val;
        }
        
        return $default_setup;
    }

    /***********************/
    /**
     */
    protected function _load_from_db($in_db_result) {
        $ret = parent::_load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'Base Class for Main Database Records.';
            $this->name = (isset($this->name) && trim($this->name)) ? trim($this->name) : "Base Class Instance ($this->_id)";
            
            if ($this->_db_object) {
                $this->owner_id = NULL;
                $this->tags = array();
                $this->_raw_payload = NULL;
        
                if (isset($in_db_result['owner_id'])) {
                    $this->owner_id = $in_db_result['owner_id'];
                }

                if (isset($in_db_result['payload']) ) {
                    $this->_raw_payload = $in_db_result['payload'];
                }
                
                for ($tag_no = 0; $tag_no < 10; $tag_no++) {
                    $key = "tag$tag_no";
                    $tag_val = (isset($in_db_result[$key])) && $in_db_result[$key] ? $in_db_result[$key] : '';
                    $this->tags[$tag_no] = $tag_val;
                }
        
                for ($i = 0; $i < 10; $i++) {
                    $tagname = 'tag'.$i;
                    $this->tags[$i] = '';
                    if (isset($in_db_result[$tagname])) {
                        $this->tags[$i] = $in_db_result[$tagname];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['owner'] = $this->owner_id;
        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            $ret[$key] = $this->tags[$tag_no];
        }
        
        $ret['payload'] = $this->_get_storage_payload();
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _get_storage_payload() {
        $ret = $this->_get_encrypted_payload();
        
        if (!$ret) {
            $ret = $this->_raw_payload;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _get_decrypted_payload(  $in_key = NULL
                                            ) {
        $ret = NULL;

        $login_item = $this->_db_object->access_object->get_login_item();

        if (isset($in_key) && $in_key && isset($login_item) && $login_item) {
            $private_key = $login_item->get_private_key();
        
            if (isset($private_key) && ($private_key == $in_key)) {
                $ret = $this->_decrypt_payload_with_private_key($private_key);
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _get_encrypted_payload() {
        $ret = NULL;

        $login_item = $this->_db_object->access_object->get_login_item();

        if (isset($login_item) && $login_item) {
            $private_key = $login_item->get_private_key();
        
            if ($private_key) {
                $ret = $this->_encrypt_payload_with_private_key($private_key);
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _encrypt_payload_with_private_key(   $in_private_key
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_encrypt($this->_raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    protected function _decrypt_payload_with_private_key(   $in_private_key
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_decrypt($this->_raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
	public function __construct(    $in_db_object = NULL,
	                                $in_db_result = NULL,
	                                $in_owner_id = NULL,
	                                $in_tags = NULL
                                ) {
        $this->owner_id = $in_owner_id;
        $this->tags = $in_tags;
        parent::__construct($in_db_object, $in_db_result);
    }
    
    /***********************/
    /**
     */
    public function set_owner_id($in_new_id
                                        ) {
        $ret = FALSE;
        
        if (isset($in_new_id)) {
            $this->owner_id = intval($in_new_id);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function set_tags($in_tags_array
                            ) {
        $ret = FALSE;
        
        if (isset($in_tags_array) && is_array($in_tags_array) && count($in_tags_array) && (11 > count($in_tags_array))) {
            $this->tags = array_map(function($in) { return strval($in); }, $in_tags_array);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function set_tag($in_tag_index,
                            $in_tag_value
                            ) {
        $ret = FALSE;
        
        $in_tag_index = intval($in_tag_index);
        
        if (isset($in_tag_value) && (10 > $in_tag_index)) {
            if (!isset($this->tags) || !$this->tags) {
                $this->tags = Array();
            }
            $in_tag_value = strval($in_tag_value);
            $this->tags[$in_tag_index] = $in_tag_value;
            
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function get_payload(    $in_key = NULL
                                ) {
        $ret = NULL;
        
        if ($in_key) {
            $ret = $this->_get_decrypted_payload($in_key);
        } else {
            $ret = $this->_raw_payload;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function set_payload(    $in_payload,
                                    $encrypted = FALSE
                                ) {
        $ret = FALSE;
        
        if ($encrypted && $in_payload) {
            $private_key = $login_item->get_private_key();
            
            if ($private_key) {
                $encrypted_payload = NULL;
            
                if (openssl_private_encrypt($in_payload, $encrypted_payload, $in_private_key)) {
                    $this->_raw_payload = $encrypted_payload;
                    $ret = $this->update_db();
                }
            }
        } else {
            $this->_raw_payload = $in_payload;
            $ret = $this->update_db();
        }
        
        return $ret;
    }
};
