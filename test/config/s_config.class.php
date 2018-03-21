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
class CO_Config {
    /***********************/
    /**
     */
    static function base_dir() {
        return dirname(dirname(dirname(__FILE__)));
    }
    
    /***********************/
    /**
     */
    static function db_class_dir() {
        return self::base_dir().'/db';
    }
    
    /***********************/
    /**
     */
    static function db_classes_class_dir() {
        return self::base_dir().'/db_classes';
    }
    
    /***********************/
    /**
     */
    static function main_class_dir() {
        return self::base_dir().'/main';
    }
    
    /***********************/
    /**
     */
    static function shared_class_dir() {
        return self::base_dir().'/shared';
    }
    
    /***********************/
    /**
     */
    static function lang_class_dir() {
        return self::base_dir().'/lang';
    }
    
    /***********************/
    /**
     */
    static function test_class_dir() {
        return self::base_dir().'/test';
    }
    
    static $lang = 'en';
    
    static $god_mode_id = 2;
    static $god_mode_password = 'BWU-HA-HAAAA-HA!';
    
    static $data_db_name = 'badger_test_data';
    static $data_db_host = 'localhost';
    static $data_db_type = 'mysql';
    static $data_db_login = 'badger_test';
    static $data_db_password = 'm8GK9C0MR1JrR8w8';

    static $sec_db_name = 'badger_test_security';
    static $sec_db_host = 'localhost';
    static $sec_db_type = 'mysql';
    static $sec_db_login = 'badger_test';
    static $sec_db_password = 'm8GK9C0MR1JrR8w8';
}
