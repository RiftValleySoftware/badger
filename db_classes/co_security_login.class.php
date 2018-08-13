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
    private     $_override_access_class;    ///< This is a special "one-shot" semaphore telling the save to override the access class.
    protected   $_api_key;                  ///< This is an API key for REST.
    
    var $login_id;
    
    /***********************/
    /**
    Generates a cryptographically secure string.
        
    \returns a randome string.
     */
    protected static function _random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-*')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
    
    /***********************/
    /**
    This sets up a new API key after the login has been successfully verified.
     */
    protected function _set_up_api_key( $key_length ///< The length (in bytes) of the key.
                                        ) {
        $temp_api_key = self::_random_str($key_length);

        $temp_api_key .= ' - '.strval(microtime(true)); // Add the current generation microtime, for key timeout.
        
        // If we are taking the IP address into consideration, then we store that, as well.
        if (isset(CO_Config::$api_key_includes_ip_address) && CO_Config::$api_key_includes_ip_address) {
            $temp_api_key .= ' - '.strtolower(strval($_SERVER['REMOTE_ADDR']));
        }
        
        $this->_api_key = strval($temp_api_key);
    }
    
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
        $default_setup['api_key'] = $this->_api_key;
        
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
        
        $ret['api_key'] = $this->_api_key;
        $ret['login_id'] = $this->login_id;
        if ($this->_override_access_class) {
            $ret['access_class'] = 'CO_Security_ID';
            $ret['object_name'] = NULL;
            $ret['ids'] = NULL;
            $this->context = NULL;
            $this->_override_access_class = false;
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
        $this->_override_access_class = false;
        parent::__construct($in_db_object, $in_db_result, $in_ids);
        $this->class_description = 'This is a security class for individual logins.';
        
        if (!isset($this->context)) {
            $this->context = Array();
        }
        
        if (!isset($this->context['lang'])) {
            $this->context['lang'] = CO_Config::$lang;
        }
            
        if (isset($in_db_result['api_key'])) {
            $this->_api_key = $in_db_result['api_key'];
        }
        
        if (intval($this->id()) == intval(CO_Config::god_mode_id())) {
            // God Mode is always forced to use the config password.
            $this->context['hashed_password'] = bin2hex(openssl_random_pseudo_bytes(4));    // Just create a randomish junk password. It will never be used.
            $this->instance_description = 'GOD MODE: '.(isset($this->name) && $this->name ? "$this->name (".$this->login_id.")" : "Unnamed Login Node (".$this->login_id.")");
        } else {
            $this->instance_description = isset($this->name) && $this->name ? "$this->name (".$this->login_id.")" : "Unnamed Login Node (".$this->login_id.")";
        }
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns true, if the instance was able to set itself up to the provided array.
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
                $this->instance_description = isset($this->name) && $this->name ? "$this->name (".$this->login_id.")" : "Unnamed Login Node (".$this->login_id.")";
            }
            
            if (isset($in_db_result['api_key'])) {
                $this->_api_key = $in_db_result['api_key'];
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns the crypted password, as a string.
     */
    public function get_crypted_password(   $in_password_to_crypt = NULL    ///< If this is not-NULL, then, instead of returning the instance's crypted PW, the given password is crypted and returned.
                                        ) {
        
        $ret = $this->context['hashed_password'];
        
        if ($in_password_to_crypt) {
            if (strlen($in_password_to_crypt) >= CO_Config::$min_pw_len) {
                $ret = password_hash($in_password_to_crypt, PASSWORD_DEFAULT);
            } else {
                $ret = false;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns true, if the presented credentials are good.
     */
    public function is_login_valid( $in_login_id,                       ///< The login ID
                                    $in_hashed_password = NULL,         ///< The password, crypt-hashed
                                    $in_raw_password = NULL,            ///< The password, cleartext.
                                    $in_dont_create_new_api_key = false ///< If true, then we don't create a new API key.
                                    ) {
        $ret = false;
        if (isset($this->login_id) && $this->login_id && ($this->login_id == $in_login_id)) {
            $api_key = $this->get_api_key();
            if ($this->id() == CO_Config::god_mode_id()) {
                if ($in_hashed_password == $api_key) { // We have a special provision that allows the God hashed password to use the API key.
                    $ret = true;
                } else {    // God mode uses the cleartext password in the config file.
                    if ($in_raw_password && !$in_dont_create_new_api_key && isset(CO_Config::$block_logins_for_valid_api_key) && CO_Config::$block_logins_for_valid_api_key && $api_key) {
                        return false;
                    } else {
                        $ret = ($in_raw_password == CO_Config::god_mode_password());
                    }
                }
            } else {
                // The server can be set up to prevent users from logging in while another login is still active.
                if ($in_raw_password && !$in_dont_create_new_api_key && isset(CO_Config::$block_logins_for_valid_api_key) && CO_Config::$block_logins_for_valid_api_key && $api_key) {
                    return false;
                } elseif (isset($this->context['hashed_password']) && $this->context['hashed_password']) {
                    // First, see if this is in the hashed password.
                    if ($in_hashed_password) {
                        $ret = hash_equals($this->get_crypted_password(), $in_hashed_password);
                    } else { // If not, see if it's the raw password.
                        $ret = password_verify($in_raw_password, $this->get_crypted_password());
                    }
                }
            }
        }
        
        // Generate an API key. We can't save it yet, as we're probably not actually logged in.
        if ($ret && !$in_dont_create_new_api_key) {
            // God mode gets a longer key. It's not actually more secure, but we can use that to determine different treatment, later on.
            $this->_set_up_api_key($this->id() == CO_Config::god_mode_id() ? 80 : 64);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns true, if this object represents the database "God" object.
     */
    public function i_am_a_god() {
        return intval(CO_Config::god_mode_id()) == intval($this->id());
    }
    
    /***********************/
    /**
    \returns true, if we are the "God" login.
     */
    public function is_god() {
        return $this->id() == CO_Config::god_mode_id();
    }
    
    /***********************/
    /**
    \returns false, as we are not a manager.
     */
    public function is_manager() {
        return $this->is_god();
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
    \returns true, if the set was successful.
     */
    public function set_lang(   $in_lang_id = NULL  ///< The lang ID. This is not used for the low-level error handlers (which use the server setting). It is used to determine higher-level strings.
                            ) {
        $ret = false;
        
        if ($this->user_can_write()) {
            $this->context['lang'] = strtolower(trim(strval($in_lang_id)));
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns The associated User object, if it exists. NULL, otherwise.
     */
    public function get_user_object() {
        $ret = NULL;
        $access_instance = $this->get_access_object();
        
        // If we have a user, we also clear the user from knowing about us.
        if ($access_instance && method_exists($access_instance, 'get_user_from_login')) {
            $ret = $access_instance->get_user_from_login($this->id());
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This encrypts a cleartext password, and sets it into the record.
    
    \returns true, if the set was successful.
     */
    public function set_password_from_cleartext(    $in_cleartext_password  ///< The cleartext password. It will not be saved. Instead, the hashed password will be saved.
                                                ) {
        $ret = false;
        
        if ($this->user_can_write()) {
            $this->context['hashed_password'] = $this->get_crypted_password($in_cleartext_password);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     This tests a given API key against the stored value. It also checks time elapsed, to ensure that we are still within the login window.
     
     \returns true, if the API key is valid, and we are still within the allotted timespan for the key.
     */
    public function is_api_key_valid(   $in_api_key ///< The API key that we're testing.
                                    ) {
        $ret = ($this->get_api_key() == $in_api_key);
        
        if ($ret && !$this->error) {
            $this->error = new LGV_Error(   CO_Lang_Common::$login_error_code_api_key_mismatch,
                                            CO_Lang::$login_error_name_api_key_mismatch,
                                            CO_Lang::$login_error_desc_api_key_mismatch
                                        );
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns a string, with the API key, if the key is still valid. NULL, otherwise.
     */
    public function get_api_key() {
        $ret = NULL;
    
        if (isset($this->_api_key) && $this->_api_key) {
            $api_expl = explode(' - ', trim($this->_api_key));
            $my_ip = NULL;

            // God Mode gets a different timeout.
            $timeout = floatval($this->i_am_a_god() ? CO_Config::$god_session_timeout_in_seconds : CO_Config::$session_timeout_in_seconds);
            
            // We first check to make sure that we are still within the time window. If not, then all bets are off.
            if (isset($api_expl[1]) && ((microtime(true) - floatval($api_expl[1])) <= $timeout)) {
                if (isset(CO_Config::$api_key_includes_ip_address) && CO_Config::$api_key_includes_ip_address) {    // See if we are also checking the IP address.
                    $my_ip = strtolower(strval($_SERVER['REMOTE_ADDR']));
                    if (isset($api_expl[2])) {
                        if ($api_expl[2] == $my_ip) {
                            $ret = $api_expl[0];
                        } else {
                            $this->error = new LGV_Error(   CO_Lang_Common::$login_error_code_api_key_invalid,
                                                            CO_Lang::$login_error_name_api_key_invalid,
                                                            CO_Lang::$login_error_desc_api_key_invalid
                                                        );
                        }
                    } else {
                        $this->error = new LGV_Error(   CO_Lang_Common::$login_error_code_api_key_invalid,
                                                        CO_Lang::$login_error_name_api_key_invalid,
                                                        CO_Lang::$login_error_desc_api_key_invalid
                                                    );
                    }
                } else {
                    $ret = $api_expl[0];
                }
            } elseif ($api_expl[0]) {
                $this->error = new LGV_Error(   CO_Lang_Common::$login_error_code_api_key_invalid,
                                                CO_Lang::$login_error_name_api_key_invalid,
                                                CO_Lang::$login_error_desc_api_key_invalid
                                            );
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns an integer, with the number of seconds since the API key was created. -1 if there is no API key. This will return a value, even if the API key is present, but expired.
     */
    function get_api_key_age_in_seconds() {
        $ret = -1;
        
        if (isset($this->_api_key) && $this->_api_key) {
            list($api_key, $api_time) = explode(' - ', trim($this->_api_key));
            $ret = ceil(microtime(true) - floatval($api_time));
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Removes the API Key.
    
     \returns true, if the operation was successful (even if there was no previous key).
     */
    public function clear_api_key() {
        $this->_api_key = NULL;
        
        return $this->update_db();
    }
    
    /***********************/
    /**
    We override this, because the God login can only be modified by itself. No one else.
    
    \returns true, if the current logged-in user has write permission on this record.
     */
    public function user_can_write() {
        $ret = false;
        
        // Only God can edit God.
        if ($this->i_am_a_god() && !$this->get_access_object()->god_mode()) {
            return false;
        } else {
            $ids = $this->get_access_object()->get_security_ids();
        
            $my_write_item = intval($this->write_security_id);
        
            if ((0 == $my_write_item) || $this->get_access_object()->god_mode()) {
                $ret = true;
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
    
    \returns true, if the conversion was successful.
     */
    public function delete_from_db() {
        if ($this->id() != CO_Config::god_mode_id()) {
            if ($this->user_can_write()) {
                $user_object = $this->get_user_object();
            
                if (isset($user_object) && ($user_object instanceof CO_User_Collection)) {
                    $user_object->set_login(NULL);
                }
            
                $this->read_security_id = 0;
                $this->write_security_id = -1;
                $this->api_key = NULL;
                $this->context = NULL;
                $this->name = NULL;
                $this->login_id = NULL;
                $this->_ids = Array();
                $this->_override_access_class = true;
                $ret = $this->_write_to_db();
                return $ret;
            } else {
                return false;
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$login_error_code_attempt_to_delete_god,
                                            CO_Lang::$login_error_name_attempt_to_delete_god,
                                            CO_Lang::$login_error_desc_attempt_to_delete_god
                                        );
        }
    }
};
