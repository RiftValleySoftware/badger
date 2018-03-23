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
defined( 'LGV_MD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/***************************************************************************************************************************/
/**
This is the instance class for the main "data" database.
 */
class CO_Main_Data_DB extends A_CO_DB {
    /***********************************************************************************************************************/
    /***********************/
    /**
    The initializer.
     */
	public function __construct(    $in_pdo_object,             ///< The PDO object for this database, initialized and ready.
	                                $in_access_object = NULL    ///< The access object for the database. If NULL, then no login.
                                ) {
        parent::__construct($in_pdo_object, $in_access_object);
        
        $this->table_name = 'co_data_nodes';
        
        $this->class_description = 'The main database class.';
    }
};
