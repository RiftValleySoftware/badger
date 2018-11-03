<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, The Great Rift Valley Software Company
    
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
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_SDBN_CATCHER') ) {
    define('LGV_SDBN_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_security_node.class.php');

/***************************************************************************************************************************/
/**
This is the specialized class for the generic security ID.
 */
class CO_Security_ID extends CO_Security_Node {
    protected $_special_first_time_security_exemption;

    /***********************************************************************************************************************/
    /***********************/
    /**
    Constructor.
     */
	public function __construct(    $in_db_object = NULL,   ///< This is the database instance that "owns" this record.
	                                $in_db_result = NULL    ///< This is a database-format associative array that is used to initialize this instance.
                                ) {
        parent::__construct($in_db_object, $in_db_result);
        $this->class_description = 'This is a security class for IDs.';
        $this->instance_description = isset($this->name) && $this->name ? "$this->name ($this->_id)" : "Unnamed ID Node ($this->_id)";
        $this->_special_first_time_security_exemption = true;
        $this->read_security_id = $this->id();  // These are always the case, regardless of what anyone else says.
        $this->write_security_id = -1;
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns true, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db($in_db_result) {
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret) {
            $this->read_security_id = $this->id();  // These are always the case, regardless of what anyone else says.
            $this->write_security_id = -1;
            $this->class_description = 'This is a security class for IDs.';
        }
        
        return $ret;
    }

    /***********************/
    /**
    This weird little function allows a creator to once -and only once- add an ID to itself, as long as that ID is for this object.
    This is a "Heisenberg" query. Once it's called, the security exemption is gone.
    
    returns true, if the security exemption was on.
     */
    public function security_exemption() {
        $ret = $this->_special_first_time_security_exemption;
        $this->_special_first_time_security_exemption = false;
        
        return $ret;
    }
    
    /***********************/
    /**
    This is an overload, because we also want to make sure that only cleared manager objects get to see this (or God, of course).
    \returns true, if the current logged-in user has read permission on this record.
     */
    public function user_can_read() {
        $ret = parent::user_can_read();
        
        if ($ret) {
            $ret = false;
            // We make double-damn sure that only cleared managers can see this.
            $item = $this->get_access_object()->get_login_item();
            if ($this->get_access_object()->god_mode() || (isset($item) && ($item instanceof CO_Login_Manager))) {
                $exemption = in_array($this->id(), $item->ids());
            
                $ret = ($this->get_access_object()->god_mode() || $exemption);
            }
        }
        
        return $ret;
    }
};
