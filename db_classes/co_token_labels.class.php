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
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_SDBN_CATCHER') ) {
    define('LGV_SDBN_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_node.class.php');

/***************************************************************************************************************************/
/**
This class is used to assign string labels to security tokens.
It is edited by authorized managers.
It uses the instance context to store the labels.

This is a weird class. Even though it lives in BADGER, it needs to be tested by COBRA. It will crash BADGER, if BADGER is not instantiated into COBRA.
 */
class CO_Token_Labels extends CO_Security_Node {
    /***********************************************************************************************************************/
    /***********************/
    /**
    Constructor.
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL    ///< This is a database-format associative array that is used to initialize this instance.
                                ) {
        $this->context['token_strings'] = Array();
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'This is a class for displaying strings associated with security tokens';
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Token String Node ($this->_id)";
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns TRUE, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db($in_db_result) {
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'This is an abstract base class for displaying strings associated with security tokens';
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a getter for the "owner" login manager ID. This allows us to "see" the ID, even if we aren't cleared for it, as it's used as a lookup.
    
    \returns the manager ID.
     */
    public function get_manager_id() {
        return intval($this->context['manager_id']);
    }
    
    /***********************/
    /**
    This is a security-vetted getter for the login manager object. It will return NULL if we can't see the manager login.
    
    \returns the CO_Login_Manager instance, or NULL.
     */
    public function get_manager_object() {
        $m_id = $this->get_manager_id();
        
        if ($m_id) {
            return $this->get_access_object()->get_login_item($m_id);
        }
        
        return NULL;
    }
    
    /***********************/
    /**
    This sets the ID of the "owner" manager. It's security-vetted.
    
    We can set a manager ID that we can't "see," but this operation should only be done by the "manager" that owns this, or another manager.
    
    \returns TRUE, if the set was successful.
     */
    public function set_manager_id( $in_manager_id  ///< This is the integer security DB ID of the manager login that "owns" this instance.
                                    ) {
        
        if ($this->user_can_edit_ids()) {
            $this->context['manager_id'] = intval($in_manager_id);
            $ret = $this->update_db();
        }
    }
    
    /***********************/
    /**
    This is a security-vetted "getter" for a token identifier string.
    
    \returns a string, with the token identifier string. Empty string if the string does not exist, or the current user cannot see that token.
     */
    public function get_token_string(   $in_security_token_id   ///< The integer ID of the security token being queried.
                                    ) {
        $ret = '';
        
        $in_security_token_id = intval($in_security_token_id);
        
        $my_ids = $this->get_access_object()->get_security_ids();
        
        $my_ids = array_map('intval', $my_ids);
        $lang = $this->get_access_object()->get_lang();
        
        if (in_array($in_security_token_id, $my_ids)) { // We get nothing unless we can see the ID.
            if (isset($this->context['token_strings'])) {
                if (isset($this->context['token_strings'][$in_security_token_id])) {
                    if (isset($this->context['token_strings'][$in_security_token_id][$lang])) {
                        $ret = $this->context['token_strings'][$in_security_token_id][$lang]];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a security-vetted "setter" for a token identifier string.
    
    \returns TRUE, if the operation was successful.
     */
    public function set_token_string(   $in_security_token_id,      ///< The integer ID of the security token being queried.
                                        $in_security_token_label    ///< The string we want to set the name to.
                                    ) {
        $ret = FALSE;
        
        if ($this->user_can_write()) {  // First, the user has to be able to write to this instance.
            $in_security_token_id = intval($in_security_token_id);
        
            $my_ids = $this->get_access_object()->get_security_ids();
        
            $my_ids = array_map('intval', $my_ids);
        
            if (in_array($in_security_token_id, $my_ids)) { // Next, the user has to have access to the token they want to modify.
                if (!isset($this->context['token_strings'])) {
                    $this->context['token_strings'] = Array();
                }
            
                $lang = $this->get_access_object()->get_lang();
                $this->context['token_strings'][$in_security_token_id][$lang] = trim(strval($in_security_token_label));
                
                $ret = $this->update_db();
            }
        }
                
        return $ret;
    }
};
