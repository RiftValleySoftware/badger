<?php
/***************************************************************************************************************************/
/**
    BADGER Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC.
    
    This code is proprietary and confidential code, 
    It is NOT to be reused or combined into any application,
    unless done so, specifically under written license from Little Green Viper Software Development LLC.

    Little Green Viper Software Development: https://littlegreenviper.com
*/
defined( 'LGV_CONFIG_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/***************************************************************************************************************************/
/**
This file contains a trait that encompasses the majority of the required (and unaltered) methods for the standard config.

It includes settings for CHAMELEON, COBRA and ANDISOL, as well as BADGER.
 */

/// The following are values for the SSL/TLS/HTTPS settings.
define('CO_CONFIG_HTTPS_OFF', 0);               ///< This means that SSL is not required for ANY transacation. It is recommended this level be selected for testing only.
define('CO_CONFIG_HTTPS_LOGIN_ONLY', 1);        ///< SSL is only required for the initial 'login' call.
define('CO_CONFIG_HTTPS_LOGGED_IN_ONLY', 2);    ///< SSL is required for the login call, as well as all calls that include an authentication header.
define('CO_CONFIG_HTTPS_ALL', 3);               ///< SSL is required for all calls.

trait tCO_Config {
    /***********************************************************************************************************************/
    /*                                                    COMMON STUFF                                                     */
    /***********************************************************************************************************************/
    /***********************/
    /**
                                              WARNING: DANGER WILL ROBINSON DANGER
    This is a special "callback caller" for logging Database calls (PDO). The callback must be defined in the CO_Config::$_low_level_log_handler_function static variable,
    either as a function (global scope), or as an array (with element 0 being the object, itself, and element 1 being the name of the function).
    For most functions in the global scope, this will simply be the function name.
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
    static function call_low_level_log_handler_function(    $in_id,     ///< REQUIRED: The numeric login ID of the currently logged-in user..
                                                            $in_sql,    ///< REQUIRED: The SQL being sent to the PDO prepared query.
                                                            $in_params  ///< REQUIRED: Any parameters that are being sent in the prepared query.
                                                        ) {   
        $log_handler = isset(self::$_low_level_log_handler_function) ? self::$_low_level_log_handler_function : NULL;
        if (isset($log_handler) && $log_handler) {
            if (is_array($log_handler) && (1 < count($log_handler)) && is_object($log_handler[0]) && method_exists($log_handler[0], $log_handler[1])) {
                $log_handler[0]->$log_handler[1]($in_id, $in_sql, $in_params);
            } elseif (function_exists($log_handler)) {
                $log_handler($in_id, $in_sql, $in_params);
            }
        }
    }
    

    /***********************/
    /**
    This is a random string, provided by the server, to be presented with the API key. It must match the one for this server in order to approve authentication credentials.
    
    \returns the server secret as a string.
     */
    static function server_secret() {
        return isset(self::$_server_secret) ? self::$_server_secret : '';    // This just ensures that the return will be an ephemeral string, so there is no access to the original.
    }

    /***********************/
    /**
    We encapsulate this, and not the password, because this is likely to be called from methods, and this prevents it from being changed.
    
    \returns the God Mode user ID.
     */
    static function god_mode_id() {
        $id = intval(self::$_god_mode_id);  // This just ensures that the return will be an ephemeral int, so there is no access to the original.
        
        return $id;
    }

    /***********************/
    /**
    We encapsulate this, because this is likely to be called from methods, and this prevents it from being changed.
    
    \returns the God Mode user password, in cleartext.
     */
    static function god_mode_password() {
        $ret = strval(self::$_god_mode_password);  // This just ensures that the return will be an ephemeral string, so there is no access to the original.
        
        return $ret;
    }
    
    /***********************/
    /**
    Includes the given file from the database extension director[y|ies].
     */
    static function require_extension_class(    $in_filename    ///< The name of the file we want to require.
                                            ) {
        if (is_array(self::db_classes_extension_class_dir())) {
            foreach (self::db_classes_extension_class_dir() as $dir) {
                if (file_exists("$dir/$in_filename")) {
                    require_once("$dir/$in_filename");
                    break;
                }
            }
        } else {
            require_once(self::db_classes_extension_class_dir().'/'.$in_filename);
        }
    }
    
    /***********************************************************************************************************************/
    /*                                                   BASALT STUFF                                                      */
    /***********************************************************************************************************************/
        
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL main access class directory.
     */
    static function basalt_base_dir() {
        return self::base_dir();
    }
        
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL main access class directory.
     */
    static function main_class_dir() {
        return self::basalt_base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL testing directory.
     */
    static function test_class_dir() {
        return self::basalt_base_dir().'/test';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL localization directory.
     */
    static function lang_class_dir() {
        return self::basalt_base_dir().'/lang';
    }
    
    /***********************************************************************************************************************/
    /*                                                   ANDISOL STUFF                                                     */
    /***********************************************************************************************************************/
        
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL main access class directory.
     */
    static function andisol_base_dir() {
        return self::base_dir().'/andisol';
    }
        
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL main access class directory.
     */
    static function andisol_main_class_dir() {
        return self::andisol_base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL testing directory.
     */
    static function andisol_test_class_dir() {
        return self::andisol_base_dir().'/test';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL localization directory.
     */
    static function andisol_lang_class_dir() {
        return self::andisol_base_dir().'/lang';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the user-defined extended database row classes (we use the COBRA extensions for ANDISOL).
     */
    static function andisol_db_classes_extension_class_dir() {
        return self::cobra_db_classes_extension_class_dir();
    }
    
    /***********************************************************************************************************************/
    /*                                                    COBRA STUFF                                                      */
    /***********************************************************************************************************************/
        
    /***********************/
    /**
    \returns the POSIX path to the COBRA main access class directory.
     */
    static function cobra_base_dir() {
        return self::andisol_base_dir().'/cobra';
    }
        
    /***********************/
    /**
    \returns the POSIX path to the COBRA main access class directory.
     */
    static function cobra_main_class_dir() {
        return self::cobra_base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the COBRA localization directory.
     */
    static function cobra_lang_class_dir() {
        return self::cobra_base_dir().'/lang';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the user-defined extended database row classes.
     */
    static function cobra_db_classes_extension_class_dir() {
        return Array(self::cobra_base_dir().'/badger_extension_classes', self::chameleon_db_classes_extension_class_dir());
    }
    
    /***********************/
    /**
    \returns the POSIX path to the CHAMELEON testing directory.
     */
    static function cobra_test_class_dir() {
        return self::cobra_base_dir().'/test';
    }
    
    /***********************/
    /**
    Includes the given file.
     */
    static function cobra_require_extension_class(   $in_filename    ///< The name of the file we want to require.
                                            ) {
        if (is_array(self::cobra_db_classes_extension_class_dir())) {
            foreach (self::cobra_db_classes_extension_class_dir() as $dir) {
                if (file_exists("$dir/$in_filename")) {
                    require_once("$dir/$in_filename");
                    break;
                }
            }
        } else {
            require_once(self::cobra_db_classes_extension_class_dir().'/'.$in_filename);
        }
    }

    /***********************************************************************************************************************/
    /*                                                  CHAMELEON STUFF                                                    */
    /***********************************************************************************************************************/

    /***********************/
    /**
    \returns the POSIX path to the main CHAMELEON database base classes.
     */
    static function chameleon_base_dir() {
        return self::cobra_base_dir().'/chameleon';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the CHAMELEON main access class directory.
     */
    static function chameleon_main_class_dir() {
        return self::chameleon_base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the CHAMELEON localization directory.
     */
    static function chameleon_lang_class_dir() {
        return self::chameleon_base_dir().'/lang';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the CHAMELEON user-defined extended database row classes.
     */
    static function chameleon_db_classes_extension_class_dir() {
        return self::chameleon_base_dir().'/badger_extension_classes';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the CHAMELEON testing directory.
     */
    static function chameleon_test_class_dir() {
        return self::chameleon_base_dir().'/test';
    }
    
    /***********************************************************************************************************************/
    /*                                                    BADGER STUFF                                                     */
    /***********************************************************************************************************************/
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER main access class directory.
     */
    static function badger_base_dir() {
        return self::chameleon_base_dir().'/badger';
    }

    /***********************/
    /**
    \returns the POSIX path to the main BADGER database base classes.
     */
    static function db_class_dir() {
        return self::badger_base_dir().'/db';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER extended database row classes.
     */
    static function db_classes_class_dir() {
        return self::badger_base_dir().'/db_classes';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER main access class directory.
     */
    static function badger_main_class_dir() {
        return self::badger_base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER main access class directory.
     */
    static function badger_shared_class_dir() {
        return self::badger_base_dir().'/shared';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER localization directory.
     */
    static function badger_lang_class_dir() {
        return self::badger_base_dir().'/lang';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER user-defined extended database row classes.
     */
    static function badger_db_classes_extension_class_dir() {
        return self::badger_base_dir().'/badger_extension_classes';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the BADGER testing directory.
     */
    static function badger_test_class_dir() {
        return self::badger_base_dir().'/test';
    }
}
