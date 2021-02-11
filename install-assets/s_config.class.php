<?php
/***************************************************************************************************************************/
/**
    BADGER Hardened Baseline Database Component
    
    © Copyright 2021, The Great Rift Valley Software Company
    
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
defined( 'LGV_CONFIG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/***************************************************************************************************************************/
/**
This example file demonstrates the implementation-dependent configuration settings.

It includes settings for CHAMELEON, COBRA and ANDISOL, as well as BADGER.
 */
require_once('<ABSOLUTE PATH TO THE BASALT /config/t_basalt_config.interface.php FILE>');

class CO_Config {
    use tCO_Basalt_Config; // These are the built-in config methods.
    
    /// These affect the overall "God Mode" login.
    static private $_god_mode_id            = 2;                        ///< God Login Security DB ID. This is private, so it can't be programmatically changed.
    static private $_god_mode_password      = '<GOD MODE PASSWORD>';    ///< Plaintext password for the God Mode ID login. This overrides anything in the ID row.
    static private $_use_personal_tokens    = true;                     ///< If TRUE, then we can use "personal IDs."
    
    static private $_login_validation_callback = NULL;  /**<    This is a special callback for validating REST logins (BASALT). For most functions in the global scope, this will simply be the function name,
                                                                or as an array (with element 0 being the object, itself, and element 1 being the name of the function).
                                                                If this will be an object method, then it should be an array, with element 0 as the object, and element 1 a string, containing the function name.
                                                                The function signature will be:
                                                                    function login_validation_callback (    $in_login_id,  ///< REQUIRED: The login ID provided.
                                                                                                            $in_password,   ///< REQUIRED: The password (in cleartext), provided.
                                                                                                            $in_server_vars ///< REQUIRED: The $_SERVER array, at the time of the call.
                                                                                                        );
                                                                The function will return a boolean, true, if the login is allowed to proceed normally, and false, if the login is to be aborted.
                                                                If false is returned, the REST login will terminate with a 403 Forbidden response.
                                                                It should be noted that there may be security, legal, ethical and resource ramifications for logging.
                                                                It is up to the implementor to ensure compliance with all constraints.
                                                        */
    
    /// These are special callbacks for logging. Read carefully. The first logs the bottom of the stack, the second, the top.
    static private $_low_level_log_handler_function = NULL;             /**<    WARNING: DANGER WILL ROBINSON DANGER
                                                                                This is a special "callback caller" for logging Database calls (PDO). For most functions in the global scope, this will simply be the function name,
                                                                                or as an array (with element 0 being the object, itself, and element 1 being the name of the function).
                                                                                If this will be an object method, then it should be an array, with element 0 as the object, and element 1 a string, containing the function name.
                                                                                The function signature will be:
                                                                                    function log_callback(  $in_id,     ///< REQUIRED: The numeric login ID of the currently logged-in user..
                                                                                                            $in_sql,    ///< REQUIRED: The SQL being sent to the PDO prepared query.
                                                                                                            $in_params  ///< REQUIRED: Any parameters that are being sent in the prepared query.
                                                                                                        );
                                                                                There is no function return.
                                                                                The function will take care of logging the SQL query in whatever fashion the user desires.
                                                                                THIS SHOULD BE DEBUG ONLY!!! There are so many security implications in leaving this on, that I can't even begin to count. Also, performance will SUCK.
                                                                                It should be noted that there may be legal, ethical and resource ramifications for logging.
                                                                                It is up to the implementor to ensure compliance with all constraints.
                                                                        */
    static private $_log_handler_function = NULL;                       /**<    This is a special callback for logging REST calls (BASALT). For most functions in the global scope, this will simply be the function name,
                                                                                or as an array (with element 0 being the object, itself, and element 1 being the name of the function).
                                                                                If this will be an object method, then it should be an array, with element 0 as the object, and element 1 a string, containing the function name.
                                                                                The function signature will be:
                                                                                    function log_callback ( $in_andisol_instance,   ///< REQUIRED: The ANDISOL instance at the time of the call.
                                                                                                            $in_server_vars         ///< REQUIRED: The $_SERVER array, at the time of the call.
                                                                                                            );
                                                                                There is no function return.
                                                                                The function will take care of logging the REST connection in whatever fashion the user desires.
                                                                                This will assume a successful ANDISOL instantiation, and is not designed to replace the traditional server logs.
                                                                                It should be noted that there may be security, legal, ethical and resource ramifications for logging.
                                                                                It is up to the implementor to ensure compliance with all constraints.
                                                                        */

    static private $_server_secret = '<ADD YOUR SERVER SECRET HERE>';   ///< This is a random string of characters that must be presented in the authentication header, along with the temporary API key.
    
    /**
    This flag, if set to true (default is false), will allow REST users to send in an address as part of a location search.
    This is ignored, if there is no $google_api_key. It should be noted that each address lookup does count against the API key quota, so that should be considered
    before enabling this functionality.
    
    If enabled, REST users will be able to send in a 'search_address_lookup=' (instead of 'search_longitude=' and 'search_latitude=') query parameter, as well as a 'search_radius=' parameter.
    */
    static $allow_address_lookup = false;    
    static $allow_general_address_lookup = false;               ///< If true (default is false), then just anyone (login not required) can do an address lookup. If false, then only logged-in users can do an address lookup. Ignored if $allow_address_lookup is false.
    static $default_region_bias = '';                           ///< A default server Region bias.

    /// These are the basic operational settings.
    static $enable_bulk_upload = false;                                 ///< If true (default is false), then the "God" admin is allowed to use the baseline 'bulk-upload' POST command.
    static $lang                            = 'en';                     ///< The default language for the server.
    static $min_pw_len                      = 8;                        ///< The minimum password length.
    static $session_timeout_in_seconds      = 3600;                     ///< API key session timeout, in seconds (integer value). Default is 1 hour.
    static $god_session_timeout_in_seconds  = 600;                      ///< API key session timeout for the "God Mode" login, in seconds (integer value). Default is 10 minutes.
    static $api_key_includes_ip_address     = true;                     ///< If true (default is false), then the API key will include the user's IP address in the generation.
    static $block_logins_for_valid_api_key  = true;                     ///< If this is true, then users cannot log in if there is an active API key in place for that user (forces the user to log out, first).
    static $ssl_requirement_level           = CO_CONFIG_HTTPS_ALL;      /** This is the level of SSL/TLS required for transactions with the server. The possible values are:
                                                                            - CO_CONFIG_HTTPS_OFF (0)               ///< This means that SSL is not required for ANY transacation. It is recommended this level be selected for testing only.
                                                                            - CO_CONFIG_HTTPS_LOGIN_ONLY (1)        ///< SSL is only required for the initial 'login' call.
                                                                            - CO_CONFIG_HTTPS_LOGGED_IN_ONLY (2)    ///< SSL is required for the login call, as well as all calls that include an authentication header.
                                                                            - CO_CONFIG_HTTPS_ALL (3)               ///< SSL is required for all calls (Default).
                                                                        */

    /// Each database has a separate setup. They can be different technologies and/or servers.
    
    /// These are for the main data database.
    static $data_db_name                    = '<DATA DB NAME>';
    static $data_db_host                    = '<DATA DB HOST>';
    static $data_db_type                    = '<mysql or pgsql>';
    static $data_db_login                   = '<DATA DB LOGIN>';
    static $data_db_password                = '<DATA DB PASSWORD>';

    /// These are for the login/security database.
    static $sec_db_name                     = '<SECURITY DB NAME>';
    static $sec_db_host                     = '<SECURITY DB HOST>';
    static $sec_db_type                     = '<mysql or pgsql>';
    static $sec_db_login                    = '<SECURITY DB LOGIN>';
    static $sec_db_password                 = '<SECURITY DB PASSWORD>';
    
    static $google_api_key                  = '<YOUR API KEY>';         /**<    This is the Google API key. It's required for CHAMELEON to do address lookups and other geocoding tasks.
                                                                                CHAMELEON requires this to have at least the Google Geocoding API enabled.
                                                                        */
    
    /***********************/
    /**
    \returns the POSIX path to the main (ANDISOL) directory.
     */
    static function base_dir() {
        return '<ABSOLUTE POSIX PATHNAME TO ANDISOL DIRECTORY>';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the RVP Additional Plugins directory.
     */
    static function extension_dir() {
        return '<ABSOLUTE POSIX PATHNAME TO RVP ADDITIONAL PLUGINS DIRECTORY>';
    }
}
