<?php
/**
    \file s_co_config.class.php
    
    \brief Static class that defines the configuration for the database setup.
*/
defined( 'LGV_CONFIG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

class CO_Config {
    static function base_dir() {
        return '<THE ABSOLUTE POSIX PATH TO THE MAIN BADGER DIRECTORY>';
    }
    
    static function db_class_dir() {
        return self::base_dir().'/db';
    }
    
    static function db_classes_class_dir() {
        return self::base_dir().'/db_classes';
    }
    
    static function main_class_dir() {
        return self::base_dir().'/main';
    }
    
    static function shared_class_dir() {
        return self::base_dir().'/shared';
    }
    
    static function lang_class_dir() {
        return self::base_dir().'/lang';
    }
    
    static function test_class_dir() {
        return self::base_dir().'/test';
    }
    
    static $lang = 'en';    // Default is English
    
    static $god_mode_id = 2;    // Default is 2 (First security item created).
    static $god_mode_password = '<ENTER YOUR PASSWORD HERE>';
    
    static $data_db_name = '<DATA DB NAME>';
    static $data_db_host = 'localhost'; // Or other server URI
    static $data_db_type = 'mysql';     // Or other server PDO driver
    static $data_db_login = '<DB LOGIN>';
    static $data_db_password = '<DB PASSWORD>';

    static $sec_db_name = '<SECURITY DB NAME>';
    static $sec_db_host = 'localhost';  // Or other server URI
    static $sec_db_type = 'mysql';      // Or other Server PDO driver
    static $sec_db_login = '<DB LOGIN>';
    static $sec_db_password = '<DB PASSWORD>';
}
