<?php
/**
    \file s_co_pdo.class.php
    
    \brief Class that implements a simplified connection to the PHP PDO toolkit.
*/
defined( 'LGV_ACCESS_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_MD_CATCHER') ) {
    define('LGV_MD_CATCHER', 1);
}

if ( !defined('LGV_SD_CATCHER') ) {
    define('LGV_SD_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_main_data_db.class.php');
require_once(CO_Config::db_class_dir().'/co_security_db.class.php');

/**
    \brief This class provides a genericized interface to the <a href="http://us.php.net/pdo">PHP PDO</a> toolkit. It is a completely static class.
 */
class CO_Access {
    static  $pdo_error_name_failed_to_open_data_db = 'Failed to open the data storage database.';
    static  $pdo_error_desc_failed_to_open_data_db = 'There was an error while trying to access the main data storage database.';

    static  $pdo_error_name_failed_to_open_security_db = 'Failed to open the security database.';
    static  $pdo_error_desc_failed_to_open_security_db = 'There was an error while trying to access the security database.';
    
    static  $pdo_error_code_failed_to_open_data_db = 100;
    static  $pdo_error_code_failed_to_open_security_db = 101;

    private $pdo_data_db;
    private $pdo_security_db;
    
    var $data_db_object;
    var $security_db_object;
    
    var $valid;
    var $error;
    
	public function __construct() {
	    $this->pdo_data_db = null;
	    $this->pdo_security_db = null;
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
            $this->pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
            $this->data_db_object = new CO_Main_Data_DB($this->pdo_data_db);
        } catch (Exception $exception) {
            $this->pdo_data_db = null;
            $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_data_db,
                                            self::$pdo_error_name_failed_to_open_data_db,
                                            self::$pdo_error_desc_failed_to_open_data_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            return;
        }
        
        try {
            $this->pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
            $this->security_db_object = new CO_Main_Data_DB($this->pdo_security_db);
        } catch (Exception $exception) {
            $this->pdo_data_db = null;
            $this->pdo_security_db = null;
	        $this->data_db_object = null;
            $this->error = new LGV_Error(   self::$pdo_error_code_failed_to_open_security_db,
                                            self::$pdo_error_name_failed_to_open_security_db,
                                            self::$pdo_error_desc_failed_to_open_security_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            return;
        }
        
        $this->valid = true;
    }
};
