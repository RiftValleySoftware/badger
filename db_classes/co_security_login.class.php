<?php
/**
*/
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_SDBN_CATCHER') ) {
    define('LGV_SDBN_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_node.class.php');

/**
 */
class CO_Security_Login extends CO_Security_Node {
    var $login_id;
    
	public function __construct(    $in_db_object,
	                                $in_db_result
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        
        $this->class_description = 'This is a security class for individual logins.';
        
        if (isset($in_db_result['login_id'])) {
            $this->login_id = $in_db_result['login_id'];
        }
        
        if ($this->id == CO_Config::$god_mode_id) {
            // God Mode is always forced to use the config password.
            $this->context['hashed_password'] = bin2hex(openssl_random_pseudo_bytes(4));    // Just create a randomish junk password. It will never be used.
            $this->instance_description = 'GOD MODE: '.(isset($this->name) && $this->name ? "$this->name ($this->id)" : "Unnamed Login Node ($this->id)");
        } else {
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->id)" : "Unnamed Login Node ($this->id)";
        }
    }
    
    public function is_login_valid( $in_login_id,
                                    $in_raw_password) {
        $ret = FALSE;
        if (isset($this->login_id) && $this->login_id && ($this->login_id == $in_login_id)) {
            if ($this->id == CO_Config::$god_mode_id) { // God mode always reads directly from the config file, and does not encrypt.
                $ret = ($in_raw_password == CO_Config::$god_mode_password);
            } else {
                if (isset($this->context['hashed_password']) && $this->context['hashed_password']) {
                    $comp = crypt($in_raw_password, $this->context['hashed_password']);
                    $ret = $comp == $this->context['hashed_password'];
                }
            }
        }
        
        return $ret;
    }
};
