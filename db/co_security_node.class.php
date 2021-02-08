<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
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
defined( 'LGV_SDBN_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADBTB_CATCHER') ) {
    define('LGV_ADBTB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db_table_base.class.php');

/***************************************************************************************************************************/
/**
This is the base class for records in the security database.
 */
class CO_Security_Node extends A_CO_DB_Table_Base {
    protected $_ids;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is called to populate the object fields for this class with default values. These use the SQL table tags.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    This method overloads (and calls) the base class method.
    
    \returns An associative array, simulating a database read.
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        return $default_setup;
    }
    
    /***********************/
    /**
    This builds up the basic section of the instance database record. It should be overloaded, and the parent called before adding new fields.
    
    This method overloads (and calls) the base class method.
    
    \returns an associative array, in database record form.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['login_id'] = NULL;
        $ret['personal_ids'] = NULL;
        $ret['ids'] = NULL;
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Initializer
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL    ///< This is a database-format associative array that is used to initialize this instance.
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'The basic class for all security nodes. This should be specialized.';
        
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed Security Node ($this->_id)";
    }
};
