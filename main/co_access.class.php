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
defined( 'LGV_ACCESS_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

define('__BADGER_VERSION__', '1.0.0.2020');

if ( !defined('LGV_MD_CATCHER') ) {
    define('LGV_MD_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_main_data_db.class.php');

if ( !defined('LGV_SD_CATCHER') ) {
    define('LGV_SD_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_db.class.php');

$lang = CO_Config::$lang;

global $g_lang_override;    // This allows us to override the configured language at initiation time.

if (isset($g_lang_override) && $g_lang_override && file_exists(CO_Config::badger_lang_class_dir().'/'.$g_lang_override.'.php')) {
    $lang = $g_lang_override;
}

$lang_file = CO_Config::badger_lang_class_dir().'/'.$lang.'.php';
$lang_common_file = CO_Config::badger_lang_class_dir().'/common.inc.php';

if ( !defined('LGV_LANG_CATCHER') ) {
    define('LGV_LANG_CATCHER', 1);
}

require_once($lang_file);
require_once($lang_common_file);

/***************************************************************************************************************************/
/**
This is the principal interface class for Badger. To use Badger, you instantiate this class with a login (or no login), and
the instance handles all the database setup and permission-setting in the background.

You then use this class as your database interface. No SQL or DB commands. It's all functions, and it should all go through this
class or instances of records that it supplies. You do not interface with the databases. This is a functional interface.

This class is designed to be specialized and subclassed. In it's "pure" form, it is extremely generic.

Badger consists of two databases: The "data" database, and the "security" database. These do not have any database-level relations.
They can both be set up on different servers, and could even be different SQL databases, as the schemas are very simple, and we use
PDO to access them.

The databases are crazy simple. Each consists of one table, with only a single schema table. Much of the specialization is done
through subclasses.

Each database record has a classname stored. This is used to instantiate the appropriate class to interpret that record.

Security is tinfoil. Not only are there two databases, with the ability to encapsulate the security database in a hardened server,
but each login is given an ACL, and that ACL determines what it can see or modify.

Each record has one code for reading, and one code for writing. If the code is not available in the logged-in user's ACL, then that
user can't see the data, or modify it. This is enforced at the SQL level. The system will not even read in records that don't match
the security key.

You set up Badger with a config file, which implements a static class with some basic parameters for use in the system. For security,
it's a good idea to locate the config file outside the HTTP tree.

You include the config file in whatever context is your main context, and include this file after that.
You should define the "LGV_ACCESS_CATCHER" define to "1", so this file will run. This file will take care of other access tokens as
necessary.
 */
class CO_Access {    
    protected $_data_db_object;     ///< This is the instance of the class representing the "data" database. This will always be instantiated.
    protected $_security_db_object; ///< This is the instance of the class representing the "scurity" database. This may not be instantiated, if there is no login.
    protected $_login_id;           ///< This is an integer, containing the security DB ID of the logged-in user. It will be NULL for no login.

    public $valid;                  ///< This will be TRUE, if the instance is "valid" (has at least an initialized "data" database).
    public $error;                  ///< If there was an error, it will be held here.
    public $class_description;      ///< This is a brief textual description of the class.
    
    public $version;                ///< This will contain the Badger Version.
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    The constructor.
     */
	public function __construct(    $in_login_id = NULL,        ///< The login ID
                                    $in_hashed_password = NULL, ///< The password, crypt-hashed
                                    $in_raw_password = NULL     ///< The password, cleartext.
	                            ) {
        $this->class_description = 'The main data access class.';
        
        $this->main_db_available = FALSE;
        $this->security_db_available = FALSE;
        $this->_login_id = NULL;
	    $this->_data_db_object = NULL;
	    $this->_security_db_object = NULL;
	    $this->error = NULL;
	    $this->valid = FALSE;
	    $this->version = __BADGER_VERSION__;
	    
	    if ( !defined('LGV_ERROR_CATCHER') ) {
            define('LGV_ERROR_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_shared_class_dir().'/error.class.php');
        
	    if ( !defined('LGV_DB_CATCHER') ) {
            define('LGV_DB_CATCHER', 1);
        }
        
        require_once(CO_Config::db_class_dir().'/co_pdo.class.php');
        
        // We only load the security DB if there was a login/password sent in.
        if ((isset($in_login_id) && $in_login_id) && ((isset($in_hashed_password) && $in_hashed_password) || (isset($in_raw_password) && $in_raw_password))) {
            try {
                $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
                $this->_security_db_object = new CO_Security_DB($pdo_security_db, $this);
                $login_record = $this->_security_db_object->get_initial_record_by_login_id($in_login_id);
                if ($this->_security_db_object->error) {
                    $this->error = $this->_security_db_object->error;
                    
                    return;
                }
                if (isset($login_record) && ($login_record instanceof CO_Security_Login)) {
                    if (!$login_record->is_login_valid($in_login_id, $in_hashed_password, $in_raw_password)) {
                        $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_invalid_login,
                                                        CO_Lang::$pdo_error_name_invalid_login,
                                                        CO_Lang::$pdo_error_desc_invalid_login);
                
                        $this->_security_db_object = NULL;
                        return;
                    }
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_invalid_login,
                                                    CO_Lang::$pdo_error_name_invalid_login,
                                                    CO_Lang::$pdo_error_desc_invalid_login);
            
                    $this->_security_db_object = NULL;
                    return;
                }
                
                $this->_login_id = $login_record->id();
            } catch (Exception $exception) {
                $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_security_db,
                                                CO_Lang::$pdo_error_name_failed_to_open_security_db,
                                                CO_Lang::$pdo_error_desc_failed_to_open_security_db,
                                                $exception->getFile(),
                                                $exception->getLine(),
                                                $exception->getMessage());
                $this->_security_db_object = NULL;
                return;
            }
        }
        
        try {
            $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
            $this->_data_db_object = new CO_Main_Data_DB($pdo_data_db, $this);
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_data_db,
                                            CO_Lang::$pdo_error_name_failed_to_open_data_db,
                                            CO_Lang::$pdo_error_desc_failed_to_open_data_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
	        $this->_data_db_object = NULL;
	        $this->_security_db_object = NULL;
            return;
        }
        
        $this->valid = TRUE;
    }
    
    /***********************/
    /**
    This is a very "raw" function that simply checks to see if any item exists for a given integer ID.
    
    This deliberately does not pass security vetting, so we're careful. It's meant to be used by collection classes for garbage collection.
    
    \returns TRUE, if an item exists for the given ID.
     */
    public function item_exists(    $in_id    ///< The ID of the item.
                                ) {
        return $this->_data_db_object->item_exists($in_id);
    }
    
    /***********************/
    /**
    This fetches the list of security tokens the currently logged-in user has available.
    This will reload any non-God Mode IDs before fetching the IDs, in order to spike privilege escalation.
    If they have God Mode, then you're pretty much screwed, anyway.
    
    \returns an array of integers, with each one representing a security token. The first element will always be the ID of the user.
     */
    public function get_security_ids() {
        $ret = Array();
        
        if ($this->god_mode()) {
            $ret = Array(-1);
        } else {
            $login_id = $this->get_login_id();
            if (isset($login_id) && $login_id && $this->_security_db_object) {
                $ret = $this->_security_db_object->get_security_ids_for_id($this->get_login_id());
                
                if ($this->_security_db_object->error) {
                    $this->error = $this->_security_db_object->error;
                    
                    $ret = Array();
                }
            }
        }
        return $ret;
    }
    
    /***********************/
    /**
    \returns TRUE, if the main "data" database is ready for use.
     */
    public function main_db_available() {
        return NULL != $this->_data_db_object;
    }
    
    /***********************/
    /**
    \returns the ID for the logged-in user.
     */
    public function get_login_id(   $in_login_id = NULL ///< The integer login ID to check. If not-NULL, then the ID of a login instance. It must be one that the current user can see.
                                ) {
        $ret = $this->_login_id;

        $in_login_id = intval($in_login_id);
        
        // See if they are looking up an ID for another login.
        if ($in_login_id) {
            $ret = 0;   // If the lookup fails, we get back 0.
            
            $record = $this->get_single_security_record_by_id($login_id);
            
            if ($record) {
                $ret = $record->id();
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This returns the actual security DB login item for the requested user (or the current logged-in user).
    
    The response is subject to standard security vetting, so there is a possibility that nothing will be returned, when there is an existing login at that ID.
    
    \returns the instance for the requested user.
     */
    public function get_login_item( $in_login_id = NULL ///< The integer login ID to check. If not-NULL, then the ID of a login instance. It must be one that the current user can see.
                                    ) {
        $login_id = $this->get_login_id();
        
        if (isset($in_login_id) && (0 < intval($in_login_id))) {
            $login_id = intval($in_login_id);
        }
        
        return $this->get_single_security_record_by_id($login_id);
    }

    /***********************/
    /**
    \returns TRUE, if the current logged-in user is "God."
     */
    public function god_mode() {
        // We look at the hard property (as opposed to using the accessor) just to avoid subclasses messing with things.
        return intval($this->_login_id) == intval(CO_Config::god_mode_id());
    }

    /***********************/
    /**
    \returns TRUE, if the given login exists, FALSE, if not, but NULL, if there is no security DB (we're not logged in). NOTE: This is not subject to security vetting.
     */
    public function check_login_exists( $in_login_id    ///< The integer login ID to check
                                        ) {
        $ret = NULL;
        
        if ($this->security_db_available()) {
            $ret = FALSE;
            
            if ($this->_security_db_object->get_initial_record_by_id(intval($in_login_id))) {
                $ret = TRUE;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
    \returns TRUE, if the given login exists, FALSE, if not, but NULL, if there is no security DB (we're not logged in). NOTE: This is not subject to security vetting.
     */
    public function check_login_exists_by_login_string( $in_login_id    ///< The string login ID to check
                                                        ) {
        $ret = NULL;
        
        if ($this->security_db_available()) {
            $ret = FALSE;
            
            if ($this->_security_db_object->get_initial_record_by_login_id($in_login_id)) {
                $ret = TRUE;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
    \returns the requested login item. This is subject to security vetting, so no item does not mean the item doesn't exist. It just means we're in the dark about that.
     */
    public function get_login_item_by_login_string( $in_login_id    ///< The string login ID to check
                                                    ) {
        $ret = NULL;
        
        if ($this->security_db_available()) {
            $ret = $this->_security_db_object->get_single_record_by_login_id($in_login_id);
        }
        
        return $ret;
    }

    /***********************/
    /**
    \returns TRUE, if the given user exists for a login, FALSE, if not, but NULL, if there is no security DB (we're not logged in). NOTE: This is not subject to security vetting.
     */
    public function check_user_exists(  $in_login_id    ///< The login ID to check
                                    ) {
        // We first check for a login.
        $ret = $this->check_login_exists($in_login_id);
        
        if (NULL == $ret) {
            $ret = FALSE;
        } elseif (TRUE == $ret) {   // If it exists, we check further, for a user with that login.
            $ret = $this->_data_db_object->see_if_user_exists($in_login_id);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This method instantiates a new, default instance of a class.
    
    The instance does not reflect a database entity until it has had its update_db() method called.
    
    \returns a new, uninitialized instance of the requested class.
     */
    public function make_new_blank_record(  $in_classname   ///< This is the name of the class to instantiate.
                                        ) {
        $ret = NULL;
        
        // We create an empty instance to test which database gets assigned.
        if ($in_classname) {
            $filename = CO_Config::db_classes_class_dir().'/'.strtolower($in_classname).'.class.php';
            if (!class_exists($in_classname)) {
                if (!file_exists($filename)) {
                    $filename = NULL;
                    
                    $dir_array = CO_Config::db_classes_extension_class_dir();
                    
                    if (!is_array($dir_array)) {
                        $dir_array = Array($dir_array);
                    }
                
                    foreach ($dir_array as $dir) {
                        $filename = $dir.'/'.strtolower($in_classname).'.class.php';                
                        if (file_exists($filename)) {
                            require_once($filename);
                            break;
                        } else {
                            $filename = NULL;
                        }
                    }
                } else {
                    require_once($filename);
                }
                
                if (!$filename) {
                    $this->error = new LGV_Error(   CO_Lang_Common::$access_error_code_class_file_not_found,
                                                    CO_Lang::$access_error_name_class_file_not_found,
                                                    CO_Lang::$access_error_desc_class_file_not_found,
                                                    __FILE__,
                                                    __LINE__,
                                                    __METHOD__
                                                );
                    return NULL;
                }
            }

            if (class_exists($in_classname) && $this->_data_db_object && $this->_security_db_object) {    // Quick test. Not allowed to do anything unless we are logged in.
                $test_instance = new $in_classname();

                if ($test_instance instanceof CO_Main_DB_Record) {
                    $ret = new $in_classname($this->_data_db_object);
                } elseif ($test_instance instanceof CO_Security_Node) {
                    $ret = new $in_classname($this->_security_db_object);
                }
        
                if ($ret) {
                    $ret->write_security_id = intval($this->get_login_id());
                    $ret->update_db();   // Make sure it gets saved.
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$access_error_code_class_not_created,
                                                    CO_Lang::$access_error_name_class_not_created,
                                                    CO_Lang::$access_error_desc_class_not_created,
                                                    __FILE__,
                                                    __LINE__,
                                                    __METHOD__
                                                );
                }
            } elseif (!$this->_security_db_object) {
                $this->error = new LGV_Error(   CO_Lang_Common::$access_error_code_user_not_authorized,
                                                CO_Lang::$access_error_name_user_not_authorized,
                                                CO_Lang::$access_error_desc_user_not_authorized,
                                                __FILE__,
                                                __LINE__,
                                                __METHOD__
                                            );
            } else {
                $this->error = new LGV_Error(   CO_Lang_Common::$access_error_code_class_not_created,
                                                CO_Lang::$access_error_name_class_not_created,
                                                CO_Lang::$access_error_desc_class_not_created,
                                                __FILE__,
                                                __LINE__,
                                                __METHOD__
                                            );
            }
        }
        
        return $ret;
    }
        
    /***********************/
    /**
    This method queries the "data" databse for multiple records, given a list of IDs.
    
    The records will not be returned if the user does not have read permission for them.
    
    \returns an array of instances, fetched an initialized from the database.
     */
    public function get_multiple_data_records_by_id(    $in_id_array    ///< An array of integers, with the item IDs.
                                                    ) {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_multiple_records_by_id($in_id_array);
        
            if ($this->_data_db_object->error) {
                $this->error = $this->_data_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
    This is a "security-safe" method for fetching a single record from the "data" database, by its ID.
    
    \returns a single new instance, initialized from the database.
     */
    public function get_single_data_record_by_id(   $in_id  ///< The ID of the record to fetch.
                                                ) {
        $ret = NULL;
        
        $tmp = $this->get_multiple_data_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }

    /***********************/
    /**
    This returns every readable (by this user) item from the "data" database.
    
    \returns an array of instances.
     */
    public function get_all_data_readable_records(  $open_only = FALSE,  ///< If TRUE, then we will look for ONLY records with a NULL or 0 read_security_id
                                                    $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                                ) {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_all_readable_records($open_only, $in_this_id);
        }
        
        return $ret;
    }

    /***********************/
    /**
    This returns every writeable (by this user) item from the "data" database.
    
    \returns an array of instances.
     */
    public function get_all_data_writeable_records( $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                                    ) {
        $ret = NULL;
        
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            $ret = $this->_data_db_object->get_all_writeable_records($in_this_id);
        
            if ($this->_data_db_object->error) {
                $this->error = $this->_data_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns TRUE if the security database is available and ready for use.
     */
    public function security_db_available() {
        return NULL != $this->_security_db_object;
    }

    /***********************/
    /**
    This method queries the "security" databse for multiple records, given a list of IDs.
    
    The records will not be returned if the user does not have read permission for them.
    
    \returns an array of instances, fetched an initialized from the database.
     */
    public function get_multiple_security_records_by_id(    $in_id_array
                                                        ) {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_multiple_records_by_id($in_id_array);
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
    This is a "security-safe" method for fetching a single record from the "security" database, by its ID.
    
    \returns a single new instance, initialized from the database.
     */
    public function get_single_security_record_by_id(   $in_id
                                                    ) {
        $ret = NULL;
        
        $tmp = $this->get_multiple_security_records_by_id(Array($in_id));
        if (isset($tmp) && is_array($tmp) && (1 == count($tmp))) {
            $ret = $tmp[0];
        }
    
        return $ret;
    }

    /***********************/
    /**
    This returns every readable (by this user) item from the "security" database.
    
    \returns an array of instances.
     */
    public function get_all_security_readable_records( $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                                        ) {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_all_readable_records($in_this_id);
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }

    /***********************/
    /**
    This returns every writeable (by this user) item from the "security" database.
    
    \returns an array of instances.
     */
    public function get_all_security_writeable_records( $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                                        ) {
        $ret = NULL;
        
        if (isset($this->_security_db_object) && $this->_security_db_object) {
            $ret = $this->_security_db_object->get_all_writeable_records($in_this_id);
        
            if ($this->_security_db_object->error) {
                $this->error = $this->_security_db_object->error;
                
                return NULL;
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This writes a data record to the "data" database, based on an associative array of elements.
    
    This is security-safe.
    
    This should generally not be called by user contexts.
    
    \returns TRUE, or the ID of a new record.
     */
    public function write_data_record(  $params_associative_array   ///< This is an associative array that has the values, keyed by the database column IDs.
                                    ) {
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            return $this->_data_db_object->write_record($params_associative_array);
        }
        
        return FALSE;
    }
    
    /***********************/
    /**
    This is a "security-safe" method for deleting a record by its ID.
    
    \returns TRUE, if the deletion succeeded.
     */
    public function delete_data_record( $id ///< The integer ID of the record to be deleted.
                                        ) {
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            return $this->_data_db_object->delete_record($id);
        }
        
        return FALSE;
    }
    
    /***********************/
    /**
    This is a "generic" data database search. It can be called from external user contexts, and allows a fairly generalized search of the "data" database.
    Sorting will be done for the "owner" and "location" values. "owner" will be sorted by the ID of the returned records, and "location" will be by distance from the center.
    
    It is "security-safe."
    
    \returns an array of instances that match the search parameters. If $count_only is TRUE, then it will be a single integer, with the count of responses to the search (if a page, then only the number of items on that page).
     */
    public function generic_search( $in_search_parameters = NULL,   /**< This is an associative array of terms to define the search. The keys should be:
                                                                        - 'id'
                                                                            This should be accompanied by an array of one or more integers, representing specific item IDs.
                                                                        - 'access_class'
                                                                            This should be accompanied by an array, containing one or more PHP class names.
                                                                        - 'name'
                                                                            This will contain a case-insensitive array of strings to check against the object_name column.
                                                                        - 'owner'
                                                                            This should be accompanied by an array of one or more integers, representing specific item IDs for "owner" objects.
                                                                        - 'tags'
                                                                            This should be accompanied by an array (up to 10 elements) of one or more case-insensitive strings, representing specific tag values.
                                                                        - 'location'
                                                                            This requires that the parameter be a 3-element associative array of floating-point numbers:
                                                                                - 'longtude'
                                                                                    This is the search center location longitude, in degrees.
                                                                                - 'latitude'
                                                                                    This is the search center location latitude, in degrees.
                                                                                - 'radius'
                                                                                    This is the search radius, in Kilometers.
                                                                    */
                                    $or_search = FALSE,             ///< If TRUE, then the search is very wide (OR), as opposed to narrow (AND), by default. If you specify a location, then that will always be AND, but the other fields can be OR.
                                    $page_size = 0,                 ///< If specified with a 1-based integer, this denotes the size of a "page" of results. NOTE: This is only applicable to MySQL or Postgres, and will be ignored if the DB is not MySQL or Postgres.
                                    $initial_page = 0,              ///< This is ignored unless $page_size is greater than 0. If so, then this 0-based index will specify which page of results to return.
                                    $and_writeable = FALSE,         ///< If TRUE, then we only want records we can modify.
                                    $count_only = FALSE,            ///< If TRUE (default is FALSE), then only a single integer will be returned, with the count of items that fit the search.
                                    $ids_only = FALSE               ///< If TRUE (default is FALSE), then the return array will consist only of integers (the object IDs). If $count_only is TRUE, this is ignored.
                                    ) {
        $ret = NULL;
        if (isset($this->_data_db_object) && $this->_data_db_object) {
            return $this->_data_db_object->generic_search($in_search_parameters, $or_search, $page_size, $initial_page, $and_writeable, $count_only, $ids_only);
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a security-vetted search for all login objects (visible to the current user).
    
    \returns an array of instances.
     */
    public function get_all_login_objects ( $and_write = FALSE  ///< If TRUE, then we only want ones we have write access to.
                                            ) {
        if (!isset($this->_security_db_object) || !$this->_security_db_object) {
            return Array();
        }
        
        return $this->_security_db_object->get_all_login_objects($and_write);
    }
    
    /***********************/
    /**
    You give a security ID, and you will get all login objects that have that token in their list (or are of that ID).
    
    This is restricted to use security vetting, so only logins visible to the current login.
       
    \returns an array of instances.
     */
    public function get_all_login_objects_with_access(  $in_security_token, ///< An integer, with the requested security token.
                                                        $and_write = FALSE  ///< If TRUE, then we only want ones we have write access to.
                                                ) {
        if (!isset($this->_security_db_object) || !$this->_security_db_object) {
            return Array();
        }
        
        return $this->_security_db_object->get_all_login_objects_with_access($in_security_token, $and_write);
    }
};
