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
require_once(dirname(__FILE__).'/t_config.interface.php');  ///< You may need to change this for your installation. It is the path to the BADGER config trait file.

class CO_Config {
    use tCO_Config; // These are the built-in config methods.

    static private $_god_mode_id = 2;   ///< God Login Security DB ID. This is private, so it can't be programmatically changed.
    static private $_god_mode_password = '<GOD MODE PASSWORD>'; ///< Plaintext password for the God Mode ID login. This overrides anything in the ID row.
    
    static $lang = 'en';                ///< The default language for the server.
    static $min_pw_len = 8;             ///< The minimum password length.
    
    /// Each database has a separate setup. They can be different technologies oand/or servers.
    static $data_db_name = '<DATA DB NAME>';
    static $data_db_host = '<DATA DB HOST>';
    static $data_db_type = '<mysql or pgsql>';
    static $data_db_login = '<DATA DB LOGIN>';
    static $data_db_password = '<DATA DB PASSWORD>';

    static $sec_db_name = '<SECURITY DB NAME>';
    static $sec_db_host = '<SECURITY DB HOST>';
    static $sec_db_type = '<mysql or pgsql>';
    static $sec_db_login = '<SECURITY DB LOGIN>';
    static $sec_db_password = '<SECURITY DB PASSWORD>';

    /**
    This is the Google API key. It's required for CHAMELEON to do address lookups and other geocoding tasks.
    CHAMELEON requires this to have at least the Google Geocoding API enabled.
    */
    static $google_api_key = '<YOUR API KEY>';
    
    /***********************/
    /**
    \returns the POSIX path to the main (ANDISOL) directory.
     */
    static function base_dir() {
        return '<ABSOLUTE POSIX PATHNAME TO ANDISOL DIRECTORY>';
    }
    
}
