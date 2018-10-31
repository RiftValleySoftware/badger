<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC/The Great Rift Valley Software Company
    
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

    Little Green Viper Software Development: https://littlegreenviper.com
*/
?><div style="display:table;margin-left:auto;margin-right:auto;text-align:left">
    <?php
        prepare_databases('basic_test');
        echo('<div id="basic-login-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'basic-login-tests\')">LOGIN/READ TESTS</a></h1>');
            echo('<div class="container">');
        
                echo('<div id="test-001" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-001\')">TEST 1: Try attaching with no logins at all</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">What is expected here, is that we will have no security database (It won't even be instantiated if there's no login),</p>
                        <p class="explain">and only the publicly readable data items will be visible (items 2, 8, 9).</p>
                        <p class="explain">There will be no writable items.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-002" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-002\')">TEST 2: Try attaching with a valid login ("secondary") ID, but invalid password ("Ralph")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This should flat-out fail.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('secondary', '', 'Ralph');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-003" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-003\')">TEST 3: Try attaching with an invalid login ID ("Fred"), but valid God Mode password ("'.CO_Config::god_mode_password().'")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This should flat-out fail.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('Fred', CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-004" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-004\')">TEST 4: Try attaching with an invalid login ID ("Fred"), but valid password ("CoreysGoryStory")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This should flat-out fail.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('Fred', CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-005" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-005\')">TEST 5: Try attaching with an valid login ID ("secondary"), but invalid hashed password ("CoreysGoryStory")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This should flat-out fail.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('secondary', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-006" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-006\')">TEST 6: Try attaching with an invalid login ID ("Fred"), but valid hashed password ("CodYOzPtwxb4A")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This should flat-out fail.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('Fred', 'CodYOzPtwxb4A');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-007" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-007\')">TEST 7: Try attaching with a valid God Mode login ID ("admin"), and valid God Mode password ("'.CO_Config::god_mode_password().'")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This ID should have read and write access to every single item in both databases.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('admin', NULL, CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-008" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-008\')">TEST 8: Try attaching with a valid secondary login ID ("secondary"), and a valid password ("CoreysGoryStory")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This ID has access (read and write) only to itself in the security database.</p>
                        <p class="explain">It has read-write access to items 2, 3, 5, 6, 7, 8, 9 of the main database.</p>
                        <p class="explain">It has no access to item 4 of the main database.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('secondary', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');

                echo('<div id="test-009" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-009\')">TEST 9: Try attaching with a valid tertiary login ID ("tertiary"), and a valid hashed password ("CodYOzPtwxb4A")</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This ID has access (read and write) only to itself in the security database.</p>
                        <p class="explain">It has read-write access to items 2, 4, 7, 9 of the main database.</p>
                        <p class="explain">It has read-only access to items 2, 4, 7, 8, 9 of the main database.</p>
                        <p class="explain">It has no access to items 3, 5, 6 of the main database.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_dbs('tertiary', 'CodYOzPtwxb4A');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                
                echo('<div id="test-009A" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-009A\')">TEST 9A: In God Mode, look for readable and writeable items of a certain ID.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this exercise, we log in as "God," and see what data items are readable or writeable via certain IDs.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        for ($id = 2; $id < 8; $id++) {
                            fetch_data_ids($id, 'admin', '', CO_Config::god_mode_password());
                        }
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                
                echo('<div id="test-009B" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-009B\')">TEST 9B: Try the same thing, but this time, logged in as "Secondary".</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this exercise, we log in as "Secondary," and see what data items are readable or writeable via certain IDs.</p>
                        <p class="explain">We expect to get only the items readble by the secondary login. The ID is ignored.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        for ($id = 2; $id < 8; $id++) {
                            fetch_data_ids($id, 'secondary', '', 'CoreysGoryStory');
                        }
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                
                echo('<div id="test-009C" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-009C\')">TEST 9C: Try the same thing, but this time, with no login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this exercise, we do not log in, and see what data items are readable or writeable via certain IDs.</p>
                        <p class="explain">We expect to get only the items readble by the general public. The ID is ignored.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        for ($id = 2; $id < 8; $id++) {
                            fetch_data_ids($id);
                        }
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
        echo('<div id="write-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'write-tests\')">DIRECT ACCESS OBJECT WRITE/DELETE TESTS</a></h1>');
            echo('<div class="container">');
                ?>
                <div class="main_div" style="margin-right:2em">
                    <p class="explain">These tests all use the access object to perform database operations, as opposed to going through individual class instances of rows.</p>
                </div>
                <?php
                echo('<div id="test-010" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-010\')">TEST 10: Log in and change an existing record</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, then change the name "Las Vegas" to "Lost Wages", and the first tag from "US" to "Fantasyland".</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_write_dbs('tertiary', 'CodYOzPtwxb4A');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                $new_record = 0;
                echo('<div id="test-011" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-011\')">TEST 11: Log in and create a new record</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, then create a new Long/Lat record.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        $new_record = try_new_dbs('secondary', 'CodYOzPtwxb4A');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                if ($new_record) {
                    echo('<div id="test-012" class="inner_closed">');
                        echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-012\')">TEST 12: Log in and delete that new record</a></h2>');

                        echo('<div class="main_div inner_container">');
                            ?>
                            <div class="main_div" style="margin-right:2em">
                            <p class="explain">We log in, then delete the record we just created.</p>
                            </div>
                            <?php
                            $start = microtime(true);
                            try_delete_dbs('secondary', 'CodYOzPtwxb4A', $new_record);
                            echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                        echo('</div>');
                    echo('</div>');
                }
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php    
    function fetch_data_ids($id, $in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            $test_item = $access_instance->get_all_data_readable_records(false, $id);
            echo("<h4>Test ID $id:</h4>");
            echo('<div class="inner_div">');
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                echo("<h4>These Items Are Explicitly Readable by $id:</h4>");
                                foreach ( $test_item as $item ) {
                                    display_record($item);
                                }
                            } else {
                                echo("<h4>NO ITEMS!</h4>");
                            }
                        } else {
                            echo("<h4 style=\"color:red;font-weight:bold\">NO ARRAY!</h4>");
                        }
                    }
                echo('</div>');
                $test_item = $access_instance->get_all_data_writeable_records($id);
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                echo("<h4>These Items Are Explicitly Writeable by $id:</h4>");
                                foreach ( $test_item as $item ) {
                                    display_record($item);
                                }
                            } else {
                                echo("<h4>NO ITEMS!</h4>");
                            }
                        } else {
                            echo("<h4 style=\"color:red;font-weight:bold\">NO ARRAY!</h4>");
                        }
                    }
                echo('</div>');
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_dbs($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            try_security_items($access_instance);
            try_data_items($access_instance);
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_data_items($access_instance) {
        if ($access_instance->main_db_available()) {
            echo('<div class="inner_div">');
                echo("<h4 style=\"text-align:center;margin-top:0.5em;background-color:black;color:white\">Main Database</h4>");
            
                $test_item = $access_instance->get_all_data_readable_records();
            
                echo('<div class="inner_div">');
                    echo("<h4>Get All Readable Main Database Items</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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
            
                $test_item = $access_instance->get_all_data_writeable_records();
            
                echo('<div class="inner_div">');
                    echo("<h4>Get All Writable Main Database Items</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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

                for ($id_no = 2; $id_no < 10; $id_no++) {
                    $test_item = $access_instance->get_single_data_record_by_id($id_no);
                    
                    if ($test_item) {
                        echo('<div class="inner_div">');
                            echo("<h4>Get Single Indexed Main Database Item $id_no</h4>");
                            echo('<div class="inner_div">');
                                if ( isset($test_item) ) {
                                    display_record($test_item);
                                } else {
                                    echo("<h4>NO ITEM!</h4>");
                                }
                            echo('</div>');
                        echo('</div>');
                    } else {
                        echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM $id_no IS NOT ACCESSIBLE</h4>");
                    }
                }
            
                $test_item = $access_instance->get_multiple_data_records_by_id(Array(2,4,6,8));
            
                echo('<div class="inner_div">');
                    echo("<h4>Get Multiple Indexed Main Database Items (2, 4, 6, 8)</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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
                echo('<h4 style="text-align:center;margin-top:0.5em">We do not have a main DB</h4>');
            echo('</div>');
        }
    }
    
    function try_security_items($access_instance) {
        if ($access_instance->security_db_available()) {
            echo('<div class="inner_div">');
                echo("<h4 style=\"text-align:center;margin-top:0.5em;background-color:black;color:white\">Security Database</h4>");
            
                $test_item = $access_instance->get_all_security_readable_records();
            
                echo('<div class="inner_div">');
                    echo("<h4>Get All Readable Security Database Items</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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
            
                $test_item = $access_instance->get_all_security_writeable_records();
            
                echo('<div class="inner_div">');
                    echo("<h4>Get All Writeable Security Database Items</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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

                for ($id_no = 2; $id_no < 7; $id_no++) {
                    $test_item = $access_instance->get_single_security_record_by_id($id_no);
            
                    if ($test_item) {
                        echo('<div class="inner_div">');
                            echo("<h4>Get Single Indexed Security Database Item $id_no</h4>");
                            echo('<div class="inner_div">');
                                if ( isset($test_item) ) {
                                    display_record($test_item);
                                } else {
                                    echo("<h4>NO ITEM!</h4>");
                                }
                            echo('</div>');
                        echo('</div>');
                    } else {
                        echo("<h4 style=\"color:red;font-weight:bold\">SECURITY ITEM $id_no IS NOT ACCESSIBLE</h4>");
                    }
                }

                $test_item = $access_instance->get_multiple_security_records_by_id(Array(2,3,4,6));
            
                echo('<div class="inner_div">');
                    echo("<h4>Get Multiple Security Database Items (2, 3, 4, 6)</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
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
    
    function try_write_dbs($in_login, $in_hashed_password) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password);
        
        if ($access_instance->valid) {
            try_basic_data_write($access_instance);
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_new_dbs($in_login, $in_hashed_password) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password);
        
        if ($access_instance->valid) {
            return try_basic_data_add($access_instance);
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_basic_data_write(  $access_instance
                                    ) {
        $id_no = 2;
        
        $test_item = $access_instance->get_single_data_record_by_id($id_no);
        
        if ($test_item) {
            echo('<div class="inner_div">');
                echo("<h4>BEFORE:</h4>");
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        display_record($test_item);
                    } else {
                        echo("<h4>NO ITEM!</h4>");
                    }
                echo('</div>');
            echo('</div>');
        } else {
            echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM $id_no IS NOT ACCESSIBLE</h4>");
        }
    
        $params = Array('id' => $id_no, 'object_name' => 'Lost Wages', 'tag0' => 'Fantasyland');
        
        $access_instance->write_data_record($params);
        
        if ($access_instance->error) {
            echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        } else {
            $test_item = $access_instance->get_single_data_record_by_id($id_no);
            
            if ($test_item) {
                echo('<div class="inner_div">');
                    echo("<h4>AFTER:</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            display_record($test_item);
                        } else {
                            echo("<h4>NO ITEM!</h4>");
                        }
                    echo('</div>');
                echo('</div>');
            } else {
                echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM $id_no IS NOT ACCESSIBLE</h4>");
            }
        }
    }
    
    function try_basic_data_add(  $access_instance
                                    ) {
        $params = Array('id' => 0, 'access_class' => 'CO_LL_Location', 'last_access' => '1970-01-01 00:00:00', 'object_name' => 'San Francisco', 'longitude' => -122.4194, 'latitude' => 37.7749);
        
        $id_no = $access_instance->write_data_record($params);
        
        if ($access_instance->error) {
            echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        } else {
            $test_item = $access_instance->get_single_data_record_by_id($id_no);
            
            if ($test_item) {
                echo('<div class="inner_div">');
                    echo("<h4>AFTER:</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            display_record($test_item);
                        } else {
                            echo("<h4>NO ITEM!</h4>");
                        }
                    echo('</div>');
                echo('</div>');
            } else {
                echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM $id_no IS NOT ACCESSIBLE</h4>");
            }
        }
        
        return $id_no;
    }
    
    function try_delete_dbs($in_login, $in_hashed_password, $in_id) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password);
        
        if ($access_instance->valid) {
            try_basic_data_delete($access_instance, $in_id);
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_basic_data_delete( $access_instance,
                                    $in_id
                                    ) {
        $id_no = $access_instance->delete_data_record($in_id);
        
        if ($access_instance->error) {
            echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        } else {
            echo("<h2>Successfully deleted Data item $in_id</h2>");
        }
    }
?>
