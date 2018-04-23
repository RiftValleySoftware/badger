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
    
/***************************************************************************************************************************/
/**
 */
class CO_Lang {
    static  $pdo_error_name_failed_to_open_data_db = 'Failed to open the data storage database.';
    static  $pdo_error_desc_failed_to_open_data_db = 'There was an error while trying to access the main data storage database.';

    static  $pdo_error_name_failed_to_open_security_db = 'Failed to open the security database.';
    static  $pdo_error_desc_failed_to_open_security_db = 'There was an error while trying to access the security database.';

    static  $pdo_error_name_invalid_login = 'Invalid Login.';
    static  $pdo_error_desc_invalid_login = 'The login or password provided was not valid.';
    
    static  $pdo_error_name_illegal_write_attempt = 'Illegal Database Write Attempt.';
    static  $pdo_error_desc_illegal_write_attempt = 'There was an attempt to write to a record for which the user does not have write permission.';
    
    static  $pdo_error_name_illegal_delete_attempt = 'Illegal Database delete Attempt.';
    static  $pdo_error_desc_illegal_delete_attempt = 'There was an attempt to delete a record for which the user does not have write permission.';
    
    static  $pdo_error_name_failed_delete_attempt = 'Failed Database delete Attempt.';
    static  $pdo_error_desc_failed_delete_attempt = 'There was a failure during an attempt to delete a record.';

    static  $db_error_name_class_file_not_found = 'Class file was not found.';
    static  $db_error_desc_class_file_not_found = 'The file for the class being instantiated was not found.';
    static  $db_error_name_class_not_created = 'Class was not created.';
    static  $db_error_desc_class_not_created = 'The attempt to instantiate the class failed.';
    
    static  $access_error_name_user_not_authorized = 'User Not Authorized';
    static  $access_error_desc_user_not_authorized = 'The user is not authorized to perform the requested operation.';
    static  $access_error_name_class_file_not_found = 'Class file was not found.';
    static  $access_error_desc_class_file_not_found = 'The file for the class being instantiated was not found.';
    static  $access_error_name_class_not_created = 'Class was not created.';
    static  $access_error_desc_class_not_created = 'The attempt to instantiate the class failed.';
}
?>