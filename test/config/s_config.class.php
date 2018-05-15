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
defined( 'LGV_CONFIG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/***************************************************************************************************************************/
/**
 */
define('_DB_TYPE_', 'mysql');

class CO_Config {
    /***********************************************************************************************************************/
    /*                                                     CHANGE THIS                                                     */
    /***********************************************************************************************************************/
    
    static $lang = 'en';
    static $min_pw_len = 8;                                     // The minimum password length.

    static $god_mode_password = 'BWU-HA-HAAAA-HA!';
    
    static $data_db_name = 'littlegr_badger_data';
    static $data_db_host = 'localhost';
    static $data_db_type = _DB_TYPE_;
    static $data_db_login = 'littlegr_badg';
    static $data_db_password = 'pnpbxI1aU0L(';

    static $sec_db_name = 'littlegr_badger_security';
    static $sec_db_host = 'localhost';
    static $sec_db_type = _DB_TYPE_;
    static $sec_db_login = 'littlegr_badg';
    static $sec_db_password = 'pnpbxI1aU0L(';
    
    static private $_god_mode_id = 2;                           // Default is 2 (First security item created).

    /***********************/
    /**
    \returns the God Mode user ID.
     */
    static function god_mode_id() {
        return self::$_god_mode_id;
    }
    
    /***********************/
    /**
    \returns the POSIX path to the main badger directory.
     */
    static function base_dir() {
        return dirname(dirname(dirname(__FILE__)));
    }
    
    /***********************/
    /**
    \returns the POSIX path to the user-defined extended database row classes.
     */
    static function db_classes_extension_class_dir() {
        return dirname(self::base_dir()).'/badger_extension_classes';
    }
    
    /***********************************************************************************************************************/
    /*                                                  DON'T CHANGE THIS                                                  */
    /***********************************************************************************************************************/
    /***********************/
    /**
    \returns the POSIX path to the main database base classes.
     */
    static function db_class_dir() {
        return self::base_dir().'/db';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the extended database row classes.
     */
    static function db_classes_class_dir() {
        return self::base_dir().'/db_classes';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the main access class directory.
     */
    static function badger_main_class_dir() {
        return self::base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the main access class directory.
     */
    static function badger_shared_class_dir() {
        return self::base_dir().'/shared';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the localization directory.
     */
    static function badger_lang_class_dir() {
        return self::base_dir().'/lang';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the testing directory.
     */
    static function badger_test_class_dir() {
        return self::base_dir().'/test';
    }
}
