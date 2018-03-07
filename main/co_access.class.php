<?php
/**
    \file s_co_pdo.class.php
    
    \brief Class that implements a simplified connection to the PHP PDO toolkit.
*/
defined( 'LGV_ACCESS_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
    \brief This class provides a genericized interface to the <a href="http://us.php.net/pdo">PHP PDO</a> toolkit. It is a completely static class.
 */
class CO_Access {
    private $pdo_data_db;
    private $pdo_security_db;
    private $db_error;
    
	public function __construct() {
	    $this->pdo_data_db = null;
	    $this->pdo_security_db = null;
	    $this->db_error = null;
	    
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
            echo("<h2>Data Node Database Connected!</h2>");
        } catch (Exception $exception) {
            $this->pdo_data_db = null;
            $this->db_error = new LGV_Error();
            $this->db_error->error_code = 1;
            return;
        }
        
        try {
            $this->pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
            echo("<h2>Security Database Connected!</h2>");
        } catch (Exception $exception) {
            $this->pdo_data_db = null;
            $this->pdo_security_db = null;
            $this->db_error = new LGV_Error();
            $this->db_error->error_code = 2;
            return;
        }
    }
    
    public function get_data_node_PDO() {
        return $this->pdo_data_db;
    }
    
    public function get_security_PDO() {
        return $this->pdo_security_db;
    }
    
    public function get_error() {
        return $this->db_error;
    }
};
