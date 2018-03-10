<?php
/**
*/
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_SDBN_CATCHER') ) {
    define('LGV_SDBN_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_node.class.php');

/**
 */
class CO_Security_Login extends CO_Security_Node {
    var $login_id;
    var $hashed_password;
    
	public function __construct(    $in_db_object,
	                                $in_db_result,
	                                $in_security_id_array = null
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_security_id_array);
        $this->class_description = 'This is a security class for individual logins.';
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->id)" : "Unnamed Login Node ($this->id)";
    }
};
