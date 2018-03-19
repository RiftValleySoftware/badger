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
class CO_Security_ID extends CO_Security_Node {
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        
        return $default_setup;
    }
    
	public function __construct(    $in_db_object = NULL,
	                                $in_db_result = NULL
                                ) {
        parent::__construct($in_db_object, $in_db_result);
            $this->class_description = 'This is a security class for IDs.';
            $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed ID Node ($this->_id)";
    }
};
