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
trait tCO_Config {
    /***********************************************************************************************************************/
    /*                                                    COMMON STUFF                                                     */
    /***********************************************************************************************************************/

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
        return self::base_dir().'/main';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL testing directory.
     */
    static function andisol_test_class_dir() {
        return self::base_dir().'/test';
    }
    
    /***********************/
    /**
    \returns the POSIX path to the ANDISOL localization directory.
     */
    static function andisol_lang_class_dir() {
        return self::base_dir().'/lang';
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
