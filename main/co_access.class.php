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
defined( 'LGV_ACCESS_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_MD_CATCHER') ) {
    define('LGV_MD_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_main_data_db.class.php');

if ( !defined('LGV_SD_CATCHER') ) {
    define('LGV_SD_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_db.class.php');

$lang = CO_Config::$lang;

global $g_lang_override;    // This allows us to override the configured language at initiation time.

if (isset($g_lang_override) && $g_lang_override && file_exists(CO_Config::lang_class_dir().'/'.$g_lang_override.'.php')) {
    $lang = $g_lang_override;
}

$lang_file = CO_Config::lang_class_dir().'/'.$lang.'.php';
$lang_common_file = CO_Config::lang_class_dir().'/common.inc.php';

if ( !defined('LGV_LANG_CATCHER') ) {
    define('LGV_LANG_CATCHER', 1);
}

require_once($lang_file);
require_once($lang_common_file);

/***************************************************************************************************************************/
/**
 */
class CO_Access {    
    protected $_data_db_object;
    protected $_security_db_object;
    protected $_login_id;

    public $valid;
    public $error;
    public $class_description;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
     */
    public function god_mode() {
        return intval($this->_login_id) == intval(CO_Config::$god_mode_id);
    }
    
    /***********************/
    /**
     */
	public function __construct(    $in_login_id = NULL,
	                                $in_hashed_password = NULL,
	                                $in_raw_password = NULL
	                            ) {
        $this->class_description = 'The main data access class.';
        
        $this->main_db_available = FALSE;
        $this->security_db_available = FALSE;
        $this->_login_id = NULL;
	    $this->_data_db_object = NULL;
	    $this->_security_db_object = NULL;
	    $this->error = NULL;
	    $this->valid = FALSE;
	    
	    if ( !defined('LGV_ERROR_CATCHER') ) {
            define('LGV_ERROR_CATCHER', 1);
        }
        
        require_once(CO_Config::shared_class_dir().'/error.class.php');
        
	    if ( !defined('LGV_DB_CATCHER') ) {
            define('LGV_DB_CATCHER', 1);
        }
        
        require_once(CO_Config::db_class_dir().'/co_pdo.class.php');
        
        // We only load the security DB if there was a login/password sent in.
        if ((isset($in_login_id) && $in_login_id) && ((isset($in_hashed_password) && $in_hashed_password) || (isset($in_raw_password) && $in_raw_password))) {
            try {
                $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
                $this->_security_db_object = new CO_Security_DB($pdo_security_db, $this);
                $login_record = $this->_security_db_object->get_initial_record_by_login_id($in_login_id);
                if ($this->_security_db_object->error) {
                    $this->error = $this->_security_db_object->error;
                    
                    return;
                }
                if (isset($login_record) && ($login_record instanceof CO_Security_Login)) {
                    if (!$login_record->is_login_valid($in_login_id, $in_hashed_password, $in_raw_password)) {
                        $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_invalid_login,
                                                        CO_Lang::$pdo_error_name_invalid_login,
                                                        CO_Lang::$pdo_error_desc_invalid_login);
                
                        $this->_security_db_object = NULL;
                        return;
                    }
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_invalid_login,
                                                    CO_Lang::$pdo_error_name_invalid_login,
                                                    CO_Lang::$pdo_error_desc_invalid_login);
            
                    $this->_security_db_object = NULL;
                    return;
                }
                
                $this->_login_id = $login_record->id();
            } catch (Exception $exception) {
                $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_security_db,
                                                CO_Lang::$pdo_error_name_failed_to_open_security_db,
                                                CO_Lang::$pdo_error_desc_failed_to_open_security_db,
                                                $exception->getFile(),
                                                $exception->getLine(),
                                                $exception->getMessage());
                $this->_security_db_object = NULL;
                return;
            }
        }
        
        try {
            $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
            $this->_data_db_object = new CO_Main_Data_DB($pdo_data_db, $this);
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_data_db,
                                            CO_Lang::$pdo_error_name_failed_to_open_data_db,
                                            CO_Lang::$pdo_error_desc_failed_to_open_data_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
	        $this->_data_db_object = NULL;
	        $this->_security_db_object = NULL;
            return;
        }
        
        $this->valid = TRUE;
    }
    
    /***********************/
    /**
     */
    public function get_security_ids() {
        $ret = Array();
        
        if ($this->god_mode()) {
            $ret = Array(-1);
        } else {
            if (isset($this->_login_id) && $this->_login_id && $this->_security_db_object) {
                $temp = $this->_security_db_object->get_initial_record_by_id($this->_login_id);
            
                if ($this->_security_db_object->error) {
                    $this->error = $this->_security_db_object->error;
                    
                    return NULL;
                }

                if (isset($temp) && ($temp instanceof CO_Security_Login)) {
                    $ret = $temp->ids;
                    array_unshift($ret, $temp->id()); // We unshift, so the login ID will always be first.
                    $ret = array_unique($ret);      // This makes sure we don't duplicate IDs (no harm, if so, but I definitely, positively, without a doubt, conclusively, adamantly hate repetitive redundancy).
                    sort($ret);
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function main_db_available() {
        return NULL != $this->_data_db_object;
    }
    
    /***********************/
    /**
     */
    public function get_login_item() {
        return $this->get_single_security_record_by_id($this->_login_id);
    }

    /***********************/
    /**
     */
    public function get_multiple_data_records_by_id(    $in_id_array
                                                    ) {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_multiple_records_by_id($in_id_array);
        
            if ($this->_data_db_object->error) {
                $this->error = $this->_data_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function make_new_blank_record(  $in_classname
                                        ) {
        $ret = NULL;
        
        // We create an empty instance to test which database gets assigned.
        if ($in_classname) {
            if (!class_exists($in_classname)) {
                $filename = CO_Config::db_classes_class_dir().'/'.strtolower($in_classname).'.class.php';
                require_once($filename);
            }
            
            if (class_exists($in_classname)) {
                $test_instance = new $in_classname();

                if ($this->_security_db_object && ($test_instance instanceof CO_Main_DB_Record)) {
                    $ret = new $in_classname($this->_data_db_object);
                } elseif ($this->_data_db_object && ($test_instance instanceof CO_Security_Node)) {
                    $ret = new $in_classname($this->_security_db_object);
                }
        
                if ($ret) {
                    $ret->write_security_id = intval($this->_login_id);
                    $ret->update_db();   // Make sure it gets saved.
                }
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_single_data_record_by_id(   $in_id
                                                ) {
        $ret = NULL;
        
        $tmp = $this->get_multiple_data_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_all_data_readable_records() {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_all_readable_records();
        }
        
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_all_data_writeable_records() {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_all_writeable_records();
        
            if ($this->_data_db_object->error) {
                $this->error = $this->_data_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function security_db_available() {
        return NULL != $this->_security_db_object;
    }

    /***********************/
    /**
     */
    public function get_multiple_security_records_by_id(    $in_id_array
                                                        ) {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_multiple_records_by_id($in_id_array);
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_single_security_record_by_id(   $in_id
                                                    ) {
        $ret = NULL;
        
        $tmp = $this->get_multiple_security_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_all_security_readable_records() {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_all_readable_records($this->get_security_ids());
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
     */
    public function get_all_security_writeable_records() {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_all_writeable_records($this->get_security_ids());
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
     */
    public function write_data_record(  $params_associative_array
                                    ) {
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            return $this->_data_db_object->write_record($params_associative_array);
        }
        
        return FALSE;
    }
    
    /***********************/
    /**
     */
    public function delete_data_record( $id
                                        ) {
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $this->_data_db_object->delete_record($id);
        }
    }
};
