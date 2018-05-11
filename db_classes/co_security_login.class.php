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
This is the specializing class for the login ID record type.
 */
class CO_Security_Login extends CO_Security_Node {
    private $_override_access_class;    ///< This is a special "one-shot" semaphore telling the save to override the access class.
    var $login_id;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is called to populate the object fields for this class with default values. These use the SQL table tags.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    This method overloads (and calls) the base class method.
    
    \returns An associative array, simulating a database read.
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['login_id'] = $this->login_id;
        $default_setup['object_name'] = $this->login_id;
        
        return $default_setup;
    }

    /***********************/
    /**
    This builds up the basic section of the instance database record. It should be overloaded, and the parent called before adding new fields.
    
    This method overloads (and calls) the base class method.
    
    \returns an associative array, in database record form.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['login_id'] = $this->login_id;
        if ($this->_override_access_class) {
            $ret['access_class'] = 'CO_Security_ID';
            $ret['object_name'] = NULL;
            $ret['ids'] = NULL;
            $this->context = NULL;
            $this->_override_access_class = FALSE;
        }
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Constructor
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL,   ///< This is a database-format associative array that is used to initialize this instance.
	                                $in_login_id = NULL,    ///< The login ID
	                                $in_ids = NULL          ///< An array of integers, representing the permissions this ID has.
                                ) {
        $this->login_id = $in_login_id;
        $this->_override_access_class = FALSE;
        parent::__construct($in_db_object, $in_db_result, $in_ids);
        $this->class_description = 'This is a security class for individual logins.';
        
        if (!isset($this->context)) {
            $this->context = Array();
        }
        
        if (!isset($this->context['lang'])) {
            $this->context['lang'] = CO_Config::$lang;
        }
        
        if (intval($this->id()) == intval(CO_Config::god_mode_id())) {
            // God Mode is always forced to use the config password.
            $this->context['hashed_password'] = bin2hex(openssl_random_pseudo_bytes(4));    // Just create a randomish junk password. It will never be used.
            $this->instance_description = 'GOD MODE: '.(isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Login Node ($this->_id)");
        } else {
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Login Node ($this->_id)";
        }
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns TRUE, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db($in_db_result) {
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret) {
            if (!isset($this->context)) {
                $this->context = Array();
            }
        
            if (!isset($this->context['lang'])) {
                $this->context['lang'] = CO_Config::$lang;
            }

            $this->class_description = 'This is a security class for individual logins.';
            if (isset($in_db_result['login_id'])) {
                $this->login_id = $in_db_result['login_id'];
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns TRUE, if the presented credentials are good.
     */
    public function is_login_valid( $in_login_id,               ///< The login ID
                                    $in_hashed_password = NULL, ///< The password, crypt-hashed
                                    $in_raw_password = NULL     ///< The password, cleartext.
                                    ) {
        $ret = FALSE;
        if (isset($this->login_id) && $this->login_id && ($this->login_id == $in_login_id)) {
            if ($this->id() == CO_Config::god_mode_id()) { // God mode always reads directly from the config file, and does not encrypt.
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
    
    /***********************/
    /**
    \returns TRUE, if this object represents the database "God" object.
     */
    public function i_am_a_god() {
        return intval(CO_Config::god_mode_id()) == intval($this->id());
    }
    
    /***********************/
    /**
    \returns a string, with the language ID for this login.
     */
    public function get_lang() {
        return $this->context['lang'];
    }
    
    /***********************/
    /**
    \returns TRUE, if the set was successful.
     */
    public function set_lang(   $in_lang_id = NULL  ///< The lang ID. This is not used for the low-level error handlers (which use the server setting). It is used to determine higher-level strings.
                            ) {
        $ret = FALSE;
        
        if ($this->user_can_write()) {
            $this->context['lang'] = strtolower(trim(strval($in_lang_id)));
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    We override this, because the God login can only be modified by itself. No one else.
    
    \returns TRUE, if the current logged-in user has write permission on this record.
     */
    public function user_can_write() {
        $ret = FALSE;
        
        // Only God can edit God.
        if ($this->i_am_a_god() && !$this->get_access_object()->god_mode()) {
            return FALSE;
        } else {
            $ids = $this->get_access_object()->get_security_ids();
        
            $my_write_item = intval($this->write_security_id);
        
            if ((0 == $my_write_item) || $this->get_access_object()->god_mode()) {
                $ret = TRUE;
            } else {
                if (isset($ids) && is_array($ids) && count($ids)) {
                    $ret = in_array($my_write_item, $ids);
                }
            }
        
        return $ret;
        }
    }
    
    /***********************/
    /**
    We override this, because logins never die. They just become security placeholders.
    
    \returns TRUE, if the conversion was successful.
     */
    public function delete_from_db() {
        if ($this->user_can_write()) {
            $this->read_security_id = -1;
            $this->write_security_id = -1;
            $this->context = NULL;
            $this->name = NULL;
            $this->login_id = NULL;
            $this->_ids = Array();
            $this->_override_access_class = TRUE;
            $ret = $this->_write_to_db();
            return $ret;
        } else {
            return FALSE;
        }
    }
};
