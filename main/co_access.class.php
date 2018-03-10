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
    
    static  $pdo_error_code_failed_to_open_data_db = 100;
    static  $pdo_error_code_failed_to_open_security_db = 101;

    var $data_db_object;
    var $security_db_object;
    
    var $valid;
    var $error;
    
    var $class_description;
    
	public function __construct(    $in_login_id = null,
	                                $in_password = null
	                            ) {
        $this->class_description = 'The main data access class.';
        
	    $this->data_db_object = null;
	    $this->security_db_object = null;
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
        
        try {
            $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
            $this->security_db_object = new CO_Main_Data_DB($pdo_security_db);
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_security_db,
                                            self::$pdo_error_name_failed_to_open_security_db,
                                            self::$pdo_error_desc_failed_to_open_security_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            return;
        }
        
        try {
            $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
            $this->data_db_object = new CO_Main_Data_DB($pdo_data_db);
        } catch (Exception $exception) {
	        $this->security_db_object = null;
            $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_data_db,
                                            self::$pdo_error_name_failed_to_open_data_db,
                                            self::$pdo_error_desc_failed_to_open_data_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            return;
        }
        
        $this->valid = true;
    }
    
    public function login(  $in_login_id = null,
	                                $in_password = null
	                        ) {
	    $ret = FALSE;
	    
	    if ( $this->security_db_object ) {
	    }
	    
	    return $ret;
	}
};
