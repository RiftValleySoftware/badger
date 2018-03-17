<?php
/**
*/
defined( 'LGV_LANG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.
    
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
}
?>