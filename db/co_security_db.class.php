<?php
/**
*/
defined( 'LGV_SD_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
 */
class CO_Security_DB {
    private $pdo_object;
    
	public function __construct(    $in_pdo_object
                                ) {
        $this->pdo_object = $in_pdo_object;
    }
};
