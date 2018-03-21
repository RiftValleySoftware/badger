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

if ( !defined('LGV_SDBN_CATCHER') ) {
    define('LGV_SDBN_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_node.class.php');

/***************************************************************************************************************************/
/**
 */
class CO_Security_Login extends CO_Security_Node {
    var $login_id;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['login_id'] = $this->login_id;
        
        return $default_setup;
    }

    /***********************/
    /**
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['login_id'] = $this->login_id;
        
        return $ret;
    }

    /***********************/
    /**
     */
    protected function _load_from_db($in_db_result) {
        $ret = parent::_load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'This is a security class for individual logins.';
        
            if (isset($in_db_result['login_id'])) {
                $this->login_id = $in_db_result['login_id'];
            }
        
            if ($this->id() == CO_Config::$god_mode_id) {
                // God Mode is always forced to use the config password.
                $this->context['hashed_password'] = bin2hex(openssl_random_pseudo_bytes(4));    // Just create a randomish junk password. It will never be used.
                $this->instance_description = 'GOD MODE: '.(isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Login Node ($this->_id)");
            } else {
                $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Login Node ($this->_id)";
            }
        }
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
	public function __construct(    $in_db_object = NULL,
	                                $in_db_result = NULL,
	                                $in_login_id = NULL,
	                                $in_ids = NULL
                                ) {
        $this->login_id = $in_login_id;
        parent::__construct($in_db_object, $in_db_result, $in_ids);
    }
    
    /***********************/
    /**
     */
    public function get_private_key() {
        $this->reload_from_db();
        
        $ret = (isset($this->context['p_key']) && $this->context['p_key']) ? $this->context['p_key'] : NULL;
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function set_private_key($in_private_key
                                    ) {
        $this->context['p_key'] = (isset($in_private_key) && $in_private_key) ? $in_private_key : NULL;
        
        $this->_write_to_db();
    }
    
    /***********************/
    /**
     */
    public function is_login_valid( $in_login_id,
                                    $in_hashed_password = NULL,
                                    $in_raw_password = NULL) {
        $ret = FALSE;
        if (isset($this->login_id) && $this->login_id && ($this->login_id == $in_login_id)) {
            if ($this->id() == CO_Config::$god_mode_id) { // God mode always reads directly from the config file, and does not encrypt.
                $ret = ($in_raw_password == CO_Config::$god_mode_password);
            } else {
                if (isset($this->context['hashed_password']) && $this->context['hashed_password']) {
                    // First, see if this is in the hashed password.
                    if ($in_hashed_password) {
                        $ret = ($in_hashed_password == $this->context['hashed_password']);
                    } else { // If not, see if it's the raw password.
                        $comp = crypt($in_raw_password, $this->context['hashed_password']);
                        $ret = ($comp == $this->context['hashed_password']);
                    }
                }
            }
        }
        
        return $ret;
    }
};
