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
This is the main instance base class for records in the main "data" database.
 */
class CO_Main_DB_Record extends A_CO_DB_Table_Base {
    static  $s_table_name = 'co_data_nodes';

    var $owner_id;
    var $tags;
    
    private $_raw_payload;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This prepares an associative array of database values for the object.
    
    \returns an associative array of default values, using the database keys.
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
    This sets up the instance, based on a supplied associative array of database values and keys.
    
    \returns TRUE, if sucessful.
     */
    protected function _load_from_db(   $in_db_result   ///< This is the associative array of database values.
                                    ) {
        $ret = parent::_load_from_db($in_db_result);    // Start by calling the base class version.
        
        if ($ret) { // If that went OK, we add our own two cents...
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
    This adds our data to the saved parameter associative array.
    
    \returns an associative array of instance values, using the database keys.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();    // Start with the base class.
        
        $ret['owner'] = $this->owner_id;
        for ($tag_no = 0; $tag_no < 10; $tag_no++) {
            $key = "tag$tag_no";
            if (isset($this->tags[$tag_no])) {
                $ret[$key] = $this->tags[$tag_no];
            } else {
                $ret[$key] = '';
            }
        }
        
        $ret['payload'] = $this->_get_storage_payload();
        
        return $ret;
    }
    
    /***********************/
    /**
    This gets the payload for storage, encrypting it, if necessary.
    
    This is meant for saving the payload into the database.
    
    \returns the payload, in whatever form it takes.
     */
    protected function _get_storage_payload(    $in_key = NULL  ///< This is an optional private key. If provided, the returned payload will be encrypted.
                                            ) {
        $ret = $this->_get_encrypted_payload();
        
        if (!$ret) {
            $ret = $this->_raw_payload;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is called to decrypt the payload with the provided private key.
    
    \returns the decrypted payload (NULL if failed).
     */
    protected function _decrypt_payload_with_private_key(   $in_private_key ///< The private key to use for decryption.
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_decrypt($this->_raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is called to decrypt the payload from the database.
    
    \returns the decrypted payload.
     */
    protected function _get_decrypted_payload(  $in_key ///< This is the private key to be used for decrypting. It should be the same one stored for the login.
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
    This is called to encrypt the payload (for storage in the DB). It uses a provided private key.
    
    \returns the encrypted payload.
     */
    protected function _encrypt_payload_with_private_key(   $in_private_key ///< The key to use for encryption.
                                                    ) {
        $ret = NULL;
        
        if (!openssl_private_encrypt($this->_raw_payload, $ret, $in_private_key)) {
            $ret = NULL;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is called to encrypt the payload (for storage in the DB). It uses the stored private key.
    
    \returns the encrypted payload.
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
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    The initializer.
     */
	public function __construct(    $in_db_object = NULL,   ///< The database object for this instance.
	                                $in_db_result = NULL,   ///< The database row for this instance (associative array, with database keys).
	                                $in_owner_id = NULL,    ///< The ID of the object (in the database) that "owns" this instance.
	                                $in_tags_array = NULL   ///< An array of strings, up to ten elements long, for the tags.      
                                ) {
        $this->owner_id = intval($in_owner_id);
        $this->tags = (isset($in_tags_array) && is_array($in_tags_array) && count($in_tags_array)) ? array_map(function($in) { return strval($in); }, $in_tags_array) : Array();
        parent::__construct($in_db_object, $in_db_result);
    }
    
    /***********************/
    /**
    Simple setter for the owner ID.
    
    \returns TRUE, if successful.
     */
    public function set_owner_id(   $in_new_id  ///< The new value
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
    Simple setter for the tags.
    
    \returns TRUE, if successful.
     */
    public function set_tags(   $in_tags_array  ///< An array of strings, up to ten elements long, for the tags.
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
    Setter for one tag, by index.
    
    \returns TRUE, if successful.
     */
    public function set_tag(    $in_tag_index,  ///< The index (0-based -0 through 9) of the tag to set.
                                $in_tag_value   ///< A string, with the tag value.
                            ) {
        $ret = FALSE;
        
        $in_tag_index = intval($in_tag_index);
        
        if (isset($in_tag_value) && (10 > $in_tag_index)) {
            if (!isset($this->tags) || !$this->tags) {
                $this->tags = Array();
            }
            $this->tags[$in_tag_index] = strval($in_tag_value);
            
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Returns the payload, decrypted, if requested.
    
    \returns the payload, in whatever form it takes.
     */
    public function get_payload(    $in_key = NULL  ///< This is an optional private key. If provided, the returned payload will be decrypted.
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
    Sets the payload, encrypting with the login key, if requested.
    
    \returns TRUE, if successful.
     */
    public function set_payload(    $in_payload,        ///< The raw, unencrypted payload to be stored.
                                    $encrypted = FALSE  ///< TRUE if we want the payload encrypted.
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
