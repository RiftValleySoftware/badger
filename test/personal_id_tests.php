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
if ( !defined('LGV_ACCESS_CATCHER') ) {
    define('LGV_ACCESS_CATCHER', 1);
}

?><div style="display:table;margin-left:auto;margin-right:auto;text-align:left">
    <?php
        prepare_databases('personal_id_test');
        echo('<div id="basic-login-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'basic-login-tests\')">BASIC TESTS</a></h1>');
            echo('<div class="container">');
        
                echo('<div id="test-068" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-068\')">TEST 68: Static Test</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                            <p class="explain">Log in as the God Admin, and Load In an item that has personal IDs already in the DB, then ensure they are loaded.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_basic_personal_ids('admin', '', CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
        
                echo('<div id="test-069" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-069\')">TEST 69: Changing IDs Test</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                            <p class="explain">Log in as the God Admin, remove IDs from one record, change the personal IDs of one record, and add new IDs to another.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_changing_personal_ids('admin', '', CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php    
    function try_basic_personal_ids($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        if ($access_instance->security_db_available()) {
            echo('<div class="inner_div">');
                $test_items = $access_instance->get_multiple_security_records_by_id([3,4,5]);
                echo('<div class="inner_div">');
                    echo("<h4>Make Sure that Item 3 Has 8 and 9 as Personal Tokens, Item 4 has 10 and 11, and Item 5 has no personal tokens.</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_items) ) {
                            if (is_array($test_items)) {
                                if (count($test_items)) {
                                    foreach ( $test_items as $item ) {
                                        display_record($item);
                                    }
                                } else {
                                    echo("<h4>NO ITEMS!</h4>");
                                }
                            } else {
                                echo("<h4 style=\"color:red;font-weight:bold\">NO ARRAY!</h4>");
                            }
                        } else {
                            echo("<h4 style=\"color:red;font-weight:bold\">NOTHING RETURNED!</h4>");
                        }
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        } else {
            echo('<div class="inner_div">');
                echo('<h4 style="text-align:center;margin-top:0.5em">We do not have a security DB</h4>');
            echo('</div>');
        }
    }
    
    function try_changing_personal_ids($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        if ($access_instance->security_db_available()) {
            echo('<div class="inner_div">');
                $test_items = $access_instance->get_multiple_security_records_by_id([3,4,5]);
                echo('<div class="inner_div">');
                    echo("<h4>Make Sure that Item 3 Has no Personal Tokens, Item 4 has 8 and 9, and Item 5 has 10 and 11.</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_items) ) {
                            if (is_array($test_items)) {
                                $result = $test_items[0]->set_personal_ids([]);
                                if (!is_array($result) || count($result)) {
                                    echo("<h4 style=\"color:red;font-weight:bold\">UNEXPECTED RESULT FOR ITEM 3!</h4>");
                                }
                                $result = $test_items[1]->set_personal_ids([8,9]);
                                if (!is_array($result) || 2 != count($result) || $result[0] != 8 || $result[1] != 9) {
                                    echo("<h4 style=\"color:red;font-weight:bold\">UNEXPECTED RESULT FOR ITEM 4!</h4>");
                                }
                                $result = $test_items[2]->set_personal_ids([10,11]);
                                if (!is_array($result) || 2 != count($result) || $result[0] != 10 || $result[1] != 11) {
                                    echo("<h4 style=\"color:red;font-weight:bold\">UNEXPECTED RESULT FOR ITEM 5!</h4>");
                                }
                                if (count($test_items)) {
                                    foreach ( $test_items as $item ) {
                                        display_record($item);
                                    }
                                } else {
                                    echo("<h4>NO ITEMS!</h4>");
                                }
                            } else {
                                echo("<h4 style=\"color:red;font-weight:bold\">NO ARRAY!</h4>");
                            }
                        } else {
                            echo("<h4 style=\"color:red;font-weight:bold\">NOTHING RETURNED!</h4>");
                        }
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        } else {
            echo('<div class="inner_div">');
                echo('<h4 style="text-align:center;margin-top:0.5em">We do not have a security DB</h4>');
            echo('</div>');
        }
    }
?>
