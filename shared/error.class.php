<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC/The Great Rift Valley Software Company
    
    LICENSE:
    
    FOR OPEN-SOURCE (COMMERCIAL OR FREE):
    This code is released as open source under the GNU Plublic License (GPL), Version 3.
    You may use, modify or republish this code, as long as you do so under the terms of the GPL, which requires that you also
    publish all modificanions, derivative products and license notices, along with this code.
    
    UNDER SPECIAL LICENSE, DIRECTLY FROM LITTLE GREEN VIPER OR THE GREAT RIFT VALLEY SOFTWARE COMPANY:
    It is NOT to be reused or combined into any application, nor is it to be redistributed, republished or sublicensed,
    unless done so, specifically WITH SPECIFIC, WRITTEN PERMISSION from Little Green Viper Software Development LLC,
    or The Great Rift Valley Software Company.

    Little Green Viper Software Development: https://littlegreenviper.com
    The Great Rift Valley Software Company: https://riftvalleysoftware.com

    Little Green Viper Software Development: https://littlegreenviper.com
*/
defined( 'LGV_ERROR_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/***************************************************************************************************************************/
/**
    \brief This class provides a general error report, with file, method and error information.
 */
class LGV_Error {
    var $error_code;
    var $error_name;
    var $error_description;
    var $error_file;
    var $error_line;
    var $error_detailed_description;

    /***********************************************************************************************************************/
    /***********************/
    /**
     */
	public function __construct(
                                $error_code = 0,
                                $error_name = NULL,
                                $error_description = NULL,
                                $error_file = NULL,
                                $error_line = NULL,
                                $error_detailed_description = NULL
	                            ) {
	    $this->error_code = $error_code;
	    $this->error_name = $error_name;
	    $this->error_description = $error_description;
	    $this->error_file = $error_file;
	    $this->error_line = $error_line;
	    $this->error_detailed_description = $error_detailed_description;
	}
};
