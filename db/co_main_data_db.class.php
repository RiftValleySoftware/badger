<?php
/**
*/
defined( 'LGV_MD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined('LGV_ADB_CATCHER') ) {
    define('LGV_ADB_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/a_co_db.class.php');

/**
 */
class CO_Main_Data_DB extends A_CO_DB {
	public function __construct(    $in_pdo_object
                                ) {
        parent::__construct($in_pdo_object);
        
        $this->table_name = 'co_data_nodes';
        
        $this->class_description = 'The main database class.';
    }
};
