<?php
/**
    \file co_error.class.php
    
    \brief Error report class.
*/
defined( 'LGV_ERROR_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
    \brief This class provides a general error report, with file, method and error information.
 */
class LGV_Error {
    var $error_code;
    var $error_name;
    var $error_description;
    var $error_file;
    var $error_method;

	public function __construct(
                                $error_code,
                                $error_name,
                                $error_description,
                                $error_file,
                                $error_method
	                            ) {
	    $this->error_code = $error_code;
	    $this->error_name = $error_name;
	    $this->error_description = $error_description;
	    $this->error_file = $error_file;
	    $this->error_method = $error_method;
	}
};
