<?php
/**
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

/**
 */
class CO_Access {
    static  $pdo_error_name_failed_to_open_data_db = 'Failed to open the data storage database.';
    static  $pdo_error_desc_failed_to_open_data_db = 'There was an error while trying to access the main data storage database.';

    static  $pdo_error_name_failed_to_open_security_db = 'Failed to open the security database.';
    static  $pdo_error_desc_failed_to_open_security_db = 'There was an error while trying to access the security database.';

    static  $pdo_error_name_invalid_login = 'Invalid Login.';
    static  $pdo_error_desc_invalid_login = 'The login or password provided was not valid.';
    
    static  $pdo_error_code_failed_to_open_data_db = 100;
    static  $pdo_error_code_failed_to_open_security_db = 101;
    static  $pdo_error_code_invalid_login = 102;
    
    protected $_data_db_object;
    protected $_security_db_object;
    protected $_login_id;
    
    protected function _bwuha_ha_ha() {
        return $this->_login_id == CO_Config::$god_mode_id;
    }

    public $valid;
    public $error;
    public $class_description;
    
	public function __construct(    $in_login_id = null,
	                                $in_password = null
	                            ) {
        $this->class_description = 'The main data access class.';
        
        $this->main_db_available = false;
        $this->security_db_available = false;
        $this->_login_id = null;
	    $this->_data_db_object = null;
	    $this->_security_db_object = null;
	    $this->error = null;
	    $this->valid = false;
	    
	    if ( !defined('LGV_ERROR_CATCHER') ) {
            define('LGV_ERROR_CATCHER', 1);
        }
        
        require_once(CO_Config::shared_class_dir().'/error.class.php');
        
	    if ( !defined('LGV_DB_CATCHER') ) {
            define('LGV_DB_CATCHER', 1);
        }
        
        require_once(CO_Config::db_class_dir().'/co_pdo.class.php');
        
        // We only load the security DB if there was a login/password sent in.
        if ((isset($in_login_id) && $in_login_id) && (isset($in_password) && $in_password)) {
            try {
                $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
                $this->_security_db_object = new CO_Security_DB($pdo_security_db);
                $login_record = $this->_security_db_object->get_initial_record_by_login_id($in_login_id);
                if (isset($login_record) && ($login_record instanceof CO_Security_Login)) {
                    if (!$login_record->is_login_valid($in_login_id, $in_password)) {
                        $this->error = new LGV_Error(   self::$pdo_error_code_invalid_login,
                                                        self::$pdo_error_name_invalid_login,
                                                        self::$pdo_error_desc_invalid_login);
                
                        $this->_security_db_object = null;
                        return;
                    }
                } else {
                    $this->error = new LGV_Error(   self::$pdo_error_code_invalid_login,
                                                    self::$pdo_error_name_invalid_login,
                                                    self::$pdo_error_desc_invalid_login);
            
                    $this->_security_db_object = null;
                    return;
                }
                
                $this->_login_id = $login_record->id;
            } catch (Exception $exception) {
                $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_security_db,
                                                self::$pdo_error_name_failed_to_open_security_db,
                                                self::$pdo_error_desc_failed_to_open_security_db,
                                                $exception->getFile(),
                                                $exception->getLine(),
                                                $exception->getMessage());
                $this->_security_db_object = null;
                return;
            }
        }
        
        try {
            $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
            $this->_data_db_object = new CO_Main_Data_DB($pdo_data_db);
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_data_db,
                                            self::$pdo_error_name_failed_to_open_data_db,
                                            self::$pdo_error_desc_failed_to_open_data_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
	        $this->_data_db_object = null;
	        $this->_security_db_object = null;
            return;
        }
        
        $this->valid = true;
    }
    
    public function get_security_ids() {
        $ret = Array();
        
        if ($this->_bwuha_ha_ha()) {
            $ret = Array(-1);
        } else {
            if (isset($this->_login_id) && $this->_login_id && $this->_security_db_object) {
                $temp = $this->_security_db_object->get_initial_record_by_id($this->_login_id);
            
                if (isset($temp) && ($temp instanceof CO_Security_Login)) {
                    $ret = $temp->ids;
                    array_push($ret, $temp->id);
                    $ret = array_unique($ret);
                    sort($ret);
                }
            }
        }
        
        return $ret;
    }
    
    public function main_db_available() {
        return null != $this->_data_db_object;
    }
    
    public function security_db_available() {
        return null != $this->_security_db_object;
    }

    public function get_multiple_data_records_by_id(   $in_id_array
                                                        ) {
        $ret = null;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_multiple_records_by_id($in_id_array, $this->get_security_ids());
        }
        
        return $ret;
    }

    public function get_multiple_security_records_by_id(   $in_id_array
                                                        ) {
        $ret = null;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_multiple_records_by_id($in_id_array, $this->get_security_ids());
        }
        
        return $ret;
    }

    public function get_single_security_record_by_id(   $in_id
                                                    ) {
        $ret = null;
        
        $tmp = $this->get_multiple_security_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }

    public function get_single_data_record_by_id(   $in_id
                                                    ) {
        $ret = null;
        
        $tmp = $this->get_multiple_data_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }
};
