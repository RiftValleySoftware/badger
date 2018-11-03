<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, The Great Rift Valley Software Company
    
    LICENSE:
    
    MIT License
    
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
    modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
    IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
    CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

    The Great Rift Valley Software Company: https://riftvalleysoftware.com
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
    
    static  $db_error_name_user_not_authorized = 'User Not Authorized';
    static  $db_error_desc_user_not_authorized = 'The user is not authorized to perform the requested operation.';
    
    static  $access_error_name_user_not_authorized = 'User Not Authorized';
    static  $access_error_desc_user_not_authorized = 'The user is not authorized to perform the requested operation.';
    static  $access_error_name_class_file_not_found = 'Class file was not found.';
    static  $access_error_desc_class_file_not_found = 'The file for the class being instantiated was not found.';
    static  $access_error_name_class_not_created = 'Class was not created.';
    static  $access_error_desc_class_not_created = 'The attempt to instantiate the class failed.';
    
    static  $login_error_name_api_key_invalid = 'API Key Invalid';
    static  $login_error_desc_api_key_invalid = 'The API key is either invalid, or has expired. You need to log in again, and acquire a new API key.';
    static  $login_error_name_api_key_mismatch = 'API Key Mismatch';
    static  $login_error_desc_api_key_mismatch = 'The API key does not match the API key for this instance.';
    
    static  $login_error_name_attempt_to_delete_god = 'Attempt To Delete \'God\' Login';
    static  $login_error_desc_attempt_to_delete_god = 'You cannot delete the \'God\' login!';
}
?>