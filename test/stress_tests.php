<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC.
    
    This code is proprietary and confidential code, 
    It is NOT to be reused or combined into any application,
    unless done so, specifically under written license from Little Green Viper Software Development LLC.

    Little Green Viper Software Development: https://littlegreenviper.com
*/
?><div style="display:table;margin-left:auto;margin-right:auto;text-align:left">
    <?php
        set_time_limit ( 120 );
        prepare_databases('stress_tests');
        echo('<div id="stress-item-access-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'stress-item-access-tests\')">ACCESS ITEMS TEST</a></h1>');
            echo('<div class="container">');
                ?>
                <div class="main_div" style="margin-right:2em">
                    <p class="explain"></p>
                </div>
                <?php
                echo('<div id="test-022" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-022\')">TEST 22: Get All Readable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We do not log in (can only see publicly viewable items), and ask to see all readable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_get_all_readable_items_stress();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-023" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-023\')">TEST 23: Get All Writeable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We do not log in (can only see publicly writeable items), and ask to see all writeable items.</p>
                        <p class="explain">We expect this to return no items.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_get_all_writeable_items_stress();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-024" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-024\')">TEST 24: Log in With A Secondary Login, and Get All Readable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, and ask to see all readable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_get_all_readable_items_stress('secondary', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-025" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-025\')">TEST 25: Log in With A Secondary Login, and Get All Writeable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, and ask to see all writeable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_get_all_writeable_items_stress('secondary', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-026" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-026\')">TEST 26: Create Randomized DB.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this exercise, we log in as "God," and create a bunch of random permissions.</p>
                        </div>
                        <?php
                        echo("<h4>Log In With God Mode</h4>");
                        $start = microtime(true);
                        just_randomize_writes('admin', '', CO_Config::god_mode_password());
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-027" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-027\')">TEST 27: Randomize with a secondary login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We then log in as secondary, and make sure that we can read what needs to be read, and can't read what shouldn't be.</p>
                        </div>
                        <?php
                        echo("<h4>Log In With Secondary</h4>");
                        $start = microtime(true);
                        just_randomize_writes('secondary', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-028" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-028\')">TEST 28: Randomize with tertiary login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We then log in as tertiary, and make sure that we can read what needs to be read, and can't read what shouldn't be.</p>
                        </div>
                        <?php
                        echo("<h4>Log In With Tertiary</h4>");
                        $start = microtime(true);
                        just_randomize_writes('tertiary', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-029" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-029\')">TEST 29: Randomize with BillyBob login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We then log in as BillyBob, and make sure that we can read what needs to be read, and can't read what shouldn't be.</p>
                        </div>
                        <?php
                        echo("<h4>Log In With BillyBob</h4>");
                        $start = microtime(true);
                        just_randomize_writes('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-030" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-030\')">TEST 30: Now, see what is publicly viewable.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We do not log in (can only see publicly viewable items), and ask to see all readable items.</p>
                        <p class="explain">We expect this to return many items, but fewer than the first time.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_get_all_readable_items_stress();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
        echo('<div id="stress-item-generic-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'stress-item-generic-tests\')">GENERIC SEARCH TEST</a></h1>');
            echo('<div class="container">');
                echo('<div id="test-031" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-031\')">TEST 31: Try a simple generic search, looking for all long/lat objects, and no login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain"></p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_1();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-032" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-032\')">TEST 32: Try a simple generic search looking for locations tagged as "HI" (Hawaii).</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test asks for the second tag to be returned as "HI".</p>
                        <p class="explain">We expect this test to succeed with about 70 elements.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_2();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-033" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-033\')">TEST 33: Try a simple generic search looking for locations tagged as "HI" (Hawaii), but this time, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test asks for the second tag to be returned as "HI".</p>
                        <p class="explain">We expect this test to succeed with about 90 elements.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_2('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-034" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-034\')">TEST 34: Try a simple generic search looking for meetings named "Back to Basics", logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test asks for the object name to be "Back to Basics" (Case-insensitive).</p>
                        <p class="explain">We expect this test to succeed with results from all over. "Back to Basics" is a very common NA meeting name.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_3('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-035" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-035\')">TEST 35: Look for meetings in a 100KM radius of the center of Houston, with no login.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test looks for records that report themselves to be within a 100KM circle from the center of Houston.</p>
                        <p class="explain">We expect this test to succeed.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_4();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-036" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-036\')">TEST 36: Look for meetings in a 100KM radius of the center of Houston, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test looks for records that report themselves to be within a 100KM circle from the center of Houston.</p>
                        <p class="explain">We expect this test to succeed.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_4('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-037" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-037\')">TEST 37: Look for multiple meeting names, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This tests looks for meetings named "Just for Today", "Back to Basics" or "Free at Last".</p>
                        <p class="explain">We expect this test to succeed.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_5('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-038" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-038\')">TEST 38: Look for multiple states in tags, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This tests looks for meetings with state tags of "HI", "KS" or "TN".</p>
                        <p class="explain">We expect this test to succeed.</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_6('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-039" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-039\')">TEST 39: Look for multiple contiguous IDs, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This tests looks for meetings with IDs from 1 - 31.</p>
                        <p class="explain">We expect this test to succeed, but we should never get ID "1".</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_7('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-040" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-040\')">TEST 40: Look for multiple random IDs, logged in as BillyBob.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This tests looks for meetings with many different IDs (including ones out of range).</p>
                        <p class="explain">We expect this test to succeed, but we should never get ID "1".</p>
                        </div>
                        <?php
                        $start = microtime(true);
                        try_custom_query_8('billybob', '', 'CoreysGoryStory');
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(true) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php
    function try_custom_query_1($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('access_class' => 'CO_LL_Location'));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_2($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('tags' => Array(NULL, NULL, NULL, NULL, NULL, 'HI')));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_3($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('name' => 'Back to Basics'));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_4($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('location' => Array('longitude' => -95.3698, 'latitude' => 29.7604, 'radius' => 100.0)));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_5($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('name' => Array('back to basics', 'just for today', 'free at last')));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_6($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('tags' => Array(NULL, NULL, NULL, NULL, NULL, Array('HI','KS','TN'))));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_7($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('id' => Array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31)));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_custom_query_8($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->generic_search(Array('id' => Array(1,2056,32,4789,599,6542,7,8,9,10000,11432,121,13,4,156,16,17,18,19,20210,21,22,2376,2,254,26,26,26,276,28,229,300000,341)));
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            echo('<div class="inner_div">');
                if ( isset($test_item) ) {
                    if (is_array($test_item)) {
                        if (count($test_item)) {
                            echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                            $count = 0;
                            foreach ( $test_item as $item ) {
                                display_record($item);
                            }
                        }
                    }
                }
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function just_randomize_writes($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->get_all_data_readable_records(true);
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
            
            echo('<div class="inner_div">');
                echo("<h4>Get All Writable Main Database Items</h4>");
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
                                $count = 0;
                                foreach ( $test_item as $item ) {
                                    $id = $item->id();
                                    if ((1 < $id) && (9 < rand(0, 10))) {
                                        echo("<h5 style=\"margin-top:1em\">Congratulations, Item $id, You've been selected!</h5>");
                                        echo('<div class="inner_div">');
                                        echo("<h6>BEFORE:</h6>");
                                            display_record($item);
                                            $new_read = 0;
                                            $new_write = 0;
                                            
                                            if ($access_instance->god_mode()) {
                                                $new_read = rand(2, 7);
                                                $new_write = rand(2, 7);
                                            } else {
                                                $ids = $access_instance->get_security_ids();
                                                $new_read = $ids[rand(0, count($ids) - 1)];
                                                $new_write = $ids[rand(0, count($ids) - 1)];
                                            }
                                            $item->set_read_security_id($new_read);
                                            $item->set_write_security_id($new_write);
                                            
                                            if (!$item->error) {
                                                $count++;
                                                echo("<h6>AFTER:</h6>");
                                                display_record($item);
                                            } else {
                                                echo("<h4 style=\"color:red;font-weight:bold\">ERROR!</h4>");
                                                echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$item->error->error_code.') '.$item->error->error_name.' ('.$item->error->error_description.')</p>');
                                            }
                                        echo('</div>');
                                    }
                                }
                                echo("<h4>We modified $count records.</h4>");
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
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }

    function try_get_all_readable_items_stress($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->get_all_data_readable_records();

            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
        
            echo('<div class="inner_div">');
                echo("<h4>Get All Readable Main Database Items</h4>");
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
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
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    function try_get_all_writeable_items_stress($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(true);
            $test_item = $access_instance->get_all_data_writeable_records();
            $fetchTime = sprintf('%01.3f', microtime(true) - $st1);
        
            echo('<div class="inner_div">');
                echo("<h4>Get All Writeable Main Database Items</h4>");
                echo('<div class="inner_div">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                echo("<h4>We got ".count($test_item)." records in $fetchTime seconds.</h4>");
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
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
?>