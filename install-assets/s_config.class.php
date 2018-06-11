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
This example file demonstrates the implementation-dependent configuration settings.

It includes settings for CHAMELEON, COBRA and ANDISOL, as well as BADGER.
 */
require_once('<ABSOLUTE PATH TO THE BASALT /config/t_basalt_config.interface.php FILE>');

class CO_Config {
    use tCO_Basalt_Config; // These are the built-in config methods.
    
    /// These affect the overall "God Mode" login.
    static private $_god_mode_id            = 2;                        ///< God Login Security DB ID. This is private, so it can't be programmatically changed.
    static private $_god_mode_password      = '<GOD MODE PASSWORD>';    ///< Plaintext password for the God Mode ID login. This overrides anything in the ID row.
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
                                                                                It should be noted that there may be legal, ethical and resource ramifications for logging.
                                                                                It is up to the implementor to ensure compliance with all constraints.
                                                                        */

    /// These are the basic operational settings.
    static $lang                            = 'en';                     ///< The default language for the server.
    static $min_pw_len                      = 8;                        ///< The minimum password length.
    static $session_timeout_in_seconds      = 3600;                     ///< API key session timeout, in seconds (integer value). Default is 1 hour.
    static $god_session_timeout_in_seconds  = 600;                      ///< API key session timeout for the "God Mode" login, in seconds (integer value). Default is 10 minutes.
    static $require_ssl_for_authentication  = true;                     ///< If false (default is true), then the HTTP authentication can be sent over non-TLS (Should only be false for testing).
    static $require_ssl_for_all             = false;                    ///< If true (default is false), then all interactions should be SSL (If true, then $require_ssl_for_authentication is ignored).
    
    /// Each database has a separate setup. They can be different technologies oand/or servers.
    
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
