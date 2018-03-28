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
        prepare_databases('advanced_test');
        echo('<div id="advanced-item-access-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'advanced-item-access-tests\')">MORE GENERIC SEARCH TESTS</a></h1>');
            echo('<div class="container">');
                ?>
                <div class="main_div" style="margin-right:2em">
                    <p class="explain">These tests apply different combinations of parameters to the access class generic search test.</p>
                </div>
                <?php
                echo('<div id="test-041" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-041\')">TEST 41: Empty Generic Test</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This simply specifies a generic test with no parameters.</p>
                        <p class="explain">We expect this to succeed, and return 1999 items.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_1();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-042" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-042\')">TEST 42: Search for named meeting in Hawaii Only</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This looks for meetings named "Just for Today" in Hawaii (tag1 is "HI").</p>
                        <p class="explain">We expect this to succeed with 3 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_2();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-043" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-043\')">TEST 43: Starting from Las Vegas, Look for Meetings within a 400Km radius that are in NV</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This does a location search around Las Vegas, but looks for records that have a tag1 of "NV", or "Nevada" so it should not have CA meetings, or meetings that do not specify a state.</p>
                        <p class="explain">We expect this to succeed with 44 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_3();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-044" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-044\')">TEST 44: Simple "Open" Location Test</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test just specifies a long/lat in Las Vegas, and a 300Km radius.</p>
                        <p class="explain">We expect this to succeed with 149 records; which is the set of meetings we will use in the next several tests.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_4();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-045" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-045\')">TEST 45: Mixed Bag 1</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test has the same Las Vegas location and radius, but also looks just for meetings in "Loma Linda" (Tag0).</p>
                        <p class="explain">We expect this to succeed with 2 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_5();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-046" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-046\')">TEST 46: Mixed Bag 2</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this test, we have the same location and radius, but we are also looking for meetings with the name of "Boys 2 Men", located anywhere in the found data set, and with a Tag0 value of "Summerlin" or "Loma Linda" or "Trona".</p>
                        <p class="explain">We expect this to return 0 records, as the meeting "Boys 2 Men" is in Pahrump, NV, which is not in the search set.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_6();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-047" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-047\')">TEST 47: Mixed Bag 3</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This is the same as test 46, with the difference being that we are specifying it as an "OR" search. That means that we are looking for "Boys 2 Men" OR with a Tag0 value of "Summerlin" or "Loma Linda" or "Trona".</p>
                        <p class="explain">This time, we will get 10 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_7();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-048" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-048\')">TEST 48: Mixed Bag 4</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test will look for records with one specific owner (ID 9) as part of the distance set.</p>
                        <p class="explain">This time, we will get 6 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_8();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-049" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-049\')">TEST 49: Mixed Bag 5</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test will look for records with multiple specific owners (ID 9 and ID 242) as part of the distance set.</p>
                        <p class="explain">This time, we will get 22 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_9();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php
    function advanced_test_1($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search();
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_2($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('', 'HI'), 'name' => 'Just for Today'));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_3($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('', Array('Nevada', 'NV')), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 400.0)));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_4($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)), TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_5($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('Loma Linda'), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_6($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array(Array('Summerlin','Loma Linda','Trona')), 'name' => 'Boys 2 Men', 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    function advanced_test_7($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array(Array('Summerlin','Loma Linda','Trona')), 'name' => 'Boys 2 Men', 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)), TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    
    function advanced_test_8($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => 9, 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
    
    function advanced_test_9($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9,242), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
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
?>