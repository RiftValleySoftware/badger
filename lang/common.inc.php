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
defined( 'LGV_LANG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.
    
global $g_lang_override;    // This allows us to override the configured language at initiation time.

if (isset($g_lang_override) && $g_lang_override && file_exists(dirname(__FILE__).'/'.$lang.'.php')) {
    $lang = $g_lang_override;
} else {
    $lang = CO_Config::$lang;
}

require_once(dirname(__FILE__).'/'.$lang.'.php');

/***************************************************************************************************************************/
/**
 */
class CO_Lang_Common {
    static  $pdo_error_code_failed_to_open_data_db = 100;
    static  $pdo_error_code_failed_to_open_security_db = 101;
    static  $pdo_error_code_invalid_login = 102;
    static  $pdo_error_code_illegal_write_attempt = 200;
    static  $pdo_error_code_illegal_delete_attempt = 201;
    static  $pdo_error_code_failed_delete_attempt = 202;

    static  $db_error_code_class_file_not_found = 300;
    static  $db_error_code_class_not_created = 301;
    static  $db_error_code_user_not_authorized = 302;

    static  $access_error_code_user_not_authorized = 400;
    static  $access_error_code_class_file_not_found = 401;
    static  $access_error_code_class_not_created = 402;
}
?>