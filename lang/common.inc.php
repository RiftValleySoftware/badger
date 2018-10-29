<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC/The Great Rift Valley Software Company
    
    LICENSE:
    
    FOR OPEN-SOURCE (COMMERCIAL OR FREE):
    This code is released as open source under the GNU Plublic License (GPL), Version 3.
    You may use, modify or republish this code, as long as you do so under the terms of the GPL, which requires that you also
    publish all modificanions, derivative products and license notices, along with this code.
    
    UNDER SPECIAL LICENSE, DIRECTLY FROM LITTLE GREEN VIPER OR THE GREAT RIFT VALLEY SOFTWARE COMPANY:
    It is NOT to be reused or combined into any application, nor is it to be redistributed, republished or sublicensed,
    unless done so, specifically WITH SPECIFIC, WRITTEN PERMISSION from Little Green Viper Software Development LLC,
    or The Great Rift Valley Software Company.

    Little Green Viper Software Development: https://littlegreenviper.com
    The Great Rift Valley Software Company: https://riftvalleysoftware.com

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
    
    static  $login_error_code_api_key_invalid = 403;
    static  $login_error_code_api_key_mismatch = 404;
    
    static  $login_error_code_attempt_to_delete_god = 500;
}
?>