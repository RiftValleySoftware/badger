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
                echo('<div id="test-050" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-050\')">TEST 50: Mixed Bag 6</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test will look for records with multiple specific owners (ID 9 and ID 242), and the meeting name "Trona 12 Traditions" as part of the distance set.</p>
                        <p class="explain">We will get 1 record.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_10();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-051" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-051\')">TEST 51: Mixed Bag 7</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This will be same as test 50, but with an "OR" search.</p>
                        <p class="explain">We will get 22 records this time.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_11();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-052" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-052\')">TEST 52: Mixed Bag 8</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This will be same as test 50, but with no location specified.</p>
                        <p class="explain">We will still get just one record.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_12();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-053" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-053\')">TEST 53: Mixed Bag 9</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Now, we try it as an "OR" search.</p>
                        <p class="explain">Now we get 82 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_13();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-054" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-054\')">TEST 54: Mixed Bag 10</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Do a simple name search for "Just for Today".</p>
                        <p class="explain">We expect to get 48 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_14();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-055" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-055\')">TEST 55: Mixed Bag 11</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Do a simple name search for "Just for Today", but this time, we "use like".</p>
                        <p class="explain">This time, we get 69 records, as a number of new meetings are added that start with "Just for Today".</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_15();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-056" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-056\')">TEST 56: Mixed Bag 12</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Do a tag1 search for "nev".</p>
                        <p class="explain">We expect to get 0 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_16();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-057" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-057\')">TEST 57: Mixed Bag 13</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Same thing, but this time, use "like".</p>
                        <p class="explain">We expect to get 103 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_17();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-058" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-058\')">TEST 58: Mixed Bag 14</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Same thing, but this time, use "like", but applied to the query in general, not just the specific tag.</p>
                        <p class="explain">We expect to get 103 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_18();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-059" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-059\')">TEST 59: Mixed Bag 15</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We use "like", but only applied to tag1. "name" has "topic meet%" in it, but no "like", which cannot do any good.</p>
                        <p class="explain">We expect to get no records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_19();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-060" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-060\')">TEST 60: Mixed Bag 16</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We use "like", but only applied to the "name", which has "topic meet%", and "like" in it, which will work, but the tag one will not.</p>
                        <p class="explain">We expect to get no records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_20();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-061" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-061\')">TEST 61: Mixed Bag 17</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We use "like", on both of them.</p>
                        <p class="explain">We expect to get 2 records.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_22();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-062" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-062\')">TEST 62: Offset Page Test 1</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We specify a page size of 50.</p>
                        <p class="explain">We expect this to start with item 2, and run for 50 records (through item 51).</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_23();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-063" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-063\')">TEST 63: Offset Page Test 2</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this test, we do 50 items, but we offset by 3 pages (start with item 152).</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_24();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-064" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-064\')">TEST 64: Large Page Test</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this test, we declare the page size to be 1000 items, and start with page 2.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_25();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-065" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-065\')">TEST 65: Count-Only Tests</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this test, we do a few generic tests, only asking for the count.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_26();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-066" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-066\')">TEST 66: ID-Only Tests</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">In this test, we do a few generic tests, only asking for the IDs.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_27();
                        echo('<h5>The test took '. sprintf('%01.3f', microtime(TRUE) - $start) . ' seconds.</h5>');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-067" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-067\')">TEST 67: "Fuzzy Location" Tests</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">Now, we test the "Location Fuzzing" aspect of the database.</p>
                        </div>
                        <?php
                        $start = microtime(TRUE);
                        advanced_test_28();
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
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
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
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
    
    function advanced_test_10($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'name' => 'Trona 12 Traditions', 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)));
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
    
    function advanced_test_11($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'name' => 'Trona 12 Traditions', 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)), TRUE);
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
    
    function advanced_test_12($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'name' => 'Trona 12 Traditions'));
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
    
    function advanced_test_13($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'name' => 'Trona 12 Traditions'), TRUE);
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
    
    function advanced_test_14($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => 'Just for Today'));
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
    
    function advanced_test_15($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => Array('Just for Today%', 'use_like' => TRUE)));
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
    
    function advanced_test_16($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('', 'nev')));
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
    
    function advanced_test_17($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('', Array('nev%', 'use_like' => TRUE))));
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
    
    function advanced_test_18($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('tags' => Array('', 'nev%', 'use_like' => TRUE)));
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
    
    function advanced_test_19($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => 'topic meet%', 'tags' => Array('', Array('nev%', 'use_like' => TRUE))));
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
    
    function advanced_test_20($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => 'topic meet%', 'tags' => Array('', 'nev%', 'use_like' => TRUE)));
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
    
    function advanced_test_21($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => Array('topic meet%', 'use_like' => TRUE), 'tags' => Array('', 'nev%')));
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
    
    function advanced_test_22($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('name' => Array('topic meet%', 'use_like' => TRUE), 'tags' => Array('', Array('nev%', 'use_like' => TRUE))));
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
    
    function advanced_test_23($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 50, 0);
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
    
    function advanced_test_24($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 50, 3);
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
    
    function advanced_test_25($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 1000, 1);
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

    function advanced_test_26($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            echo("<p class=\"explain\">First, do the exact same test as the last one we ran (Test 64).</p>");
            echo("<p class=\"explain\">This time, we just count. We expect 999 meetings (the last page of the large page test).</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 1000, 1, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            echo('<div class="inner_div">');
                echo("<h4>RESULT: $test_item (This took $fetchTime seconds)</h4>");
            echo('</div>');
            echo("<p class=\"explain\">Now, do the exact same test, but this time for the first page (1000):</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 1000, 0, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            echo('<div class="inner_div">');
                echo("<h4>RESULT: $test_item (This took $fetchTime seconds)</h4>");
            echo('</div>');
            echo("<p class=\"explain\">Next, we look for records with an owner of \"9\". We expect 9 as a result:</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => 9), FALSE, 0, 0, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            echo('<div class="inner_div">');
                echo("<h4>RESULT: $test_item (This took $fetchTime seconds)</h4>");
            echo('</div>');
            echo("<p class=\"explain\">Now we do a location-based search, starting in Las Vegas, and looking for meetings with owners of 9 or 242.</p>");
            echo("<p class=\"explain\">We expect a result of 26:</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)), TRUE, 0, 0, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            echo('<div class="inner_div">');
                echo("<h4>RESULT: $test_item (This took $fetchTime seconds)</h4>");
            echo('</div>');
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }

    function advanced_test_27($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            echo("<p class=\"explain\">Get that second page.</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 1000, 1, FALSE, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            if (isset($test_item) && is_array($test_item) && count($test_item)) {
                echo('<div class="inner_div">');
                    echo("<h4>RESULT (This took $fetchTime seconds):</h4><div style=\"font-family: Courier, Monospace\"><div>");
                    $col = 0;
                    array_walk($test_item, function($item, $index) {
                        if (0 == $index % 10) {
                            echo('</div><div>');
                        } else {
                            echo(', ');
                        }
                        echo(intval($item));
                    });
                echo('</div></div></div>');
            }
            echo("<p class=\"explain\">Get that first page.</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(NULL, FALSE, 1000, 0, FALSE, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            if (isset($test_item) && is_array($test_item) && count($test_item)) {
                echo('<div class="inner_div">');
                    echo("<h4>RESULT (This took $fetchTime seconds):</h4><div style=\"font-family: Courier, Monospace\"><div>");
                    $col = 0;
                    array_walk($test_item, function($item, $index) {
                        if (0 == $index % 10) {
                            echo('</div><div>');
                        } else {
                            echo(', ');
                        }
                        echo(intval($item));
                    });
                echo('</div></div></div>');
            }
            echo("<p class=\"explain\">Next, we look for records with an owner of \"9\":</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => 9), FALSE, 0, 0, FALSE, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            if (isset($test_item) && is_array($test_item) && count($test_item)) {
                echo('<div class="inner_div">');
                    echo("<h4>RESULT (This took $fetchTime seconds):</h4><div style=\"font-family: Courier, Monospace\"><div>");
                    $col = 0;
                    array_walk($test_item, function($item, $index) {
                        if (0 == $index % 10) {
                            echo('</div><div>');
                        } else {
                            echo(', ');
                        }
                        echo(intval($item));
                    });
                echo('</div></div></div>');
            }
            echo("<p class=\"explain\">Now we do a location-based search, starting in Las Vegas, and looking for meetings with owners of 9 or 242.</p>");
            $st1 = microtime(TRUE);
            $test_item = $access_instance->generic_search(Array('owner' => Array(9, 242), 'location' => Array('longitude' => -115.2435726, 'latitude' => 36.1356661, 'radius' => 300.0)), TRUE, 0, 0, FALSE, FALSE, TRUE);
            $fetchTime = sprintf('%01.3f', microtime(TRUE) - $st1);
            if (isset($test_item) && is_array($test_item) && count($test_item)) {
                echo('<div class="inner_div">');
                    echo("<h4>RESULT (This took $fetchTime seconds):</h4><div style=\"font-family: Courier, Monospace\"><div>");
                    $col = 0;
                    array_walk($test_item, function($item, $index) {
                        if (0 == $index % 10) {
                            echo('</div><div>');
                        } else {
                            echo(', ');
                        }
                        echo(intval($item));
                    });
                echo('</div></div></div>');
            }
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }

    function advanced_test_28() {
        global $result_table;
        $result_table = Array();
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
        
        $god_access_instance = new CO_Access('admin', '', CO_Config::$god_mode_password);
        
        if ($god_access_instance->valid) {
            $setup_list = $god_access_instance->generic_search(Array('access_class' => 'CO_LL_Location'), FALSE, 50, 1);
            if (isset($setup_list) && is_array($setup_list) && count($setup_list)) {
                array_walk($setup_list, function($item, $index) {
                    if (isset($item) && $item && ($item instanceof CO_LL_Location)) {
                        $original_long = $item->raw_longitude();
                        $original_lat = $item->raw_latitude();
                        $fuzziness = 12.5;
                        $success = $item->set_fuzz_factor($fuzziness);
                        if ($success) {
                            global $result_table;
                            $result_table[$index]['index'] = $index;
                            $result_table[$index]['name'] = $item->name;
                            $result_table[$index]['original'] = Array($original_long, $original_lat);
                        }
                    }
                });
            }
        
            $mensch_access_instance = new CO_Access();
        
            if ($mensch_access_instance->valid) {
                $check_list = $mensch_access_instance->generic_search(Array('access_class' => 'CO_LL_Location'), FALSE, 50, 1);
                if (isset($check_list) && is_array($check_list) && count($check_list)) {
                    array_walk($check_list, function($item, $index) {
                        if (isset($item) && $item && ($item instanceof CO_LL_Location)) {
                            global $result_table;
                            $result_table[$index]['CS1'] = $item->i_can_see_clearly_now() ? '√' : 'X';
                            $result_table[$index]['fuzzed-1'] = Array($item->longitude(), $item->latitude());
                            $result_table[$index]['fuzzed-2'] = Array($item->longitude(), $item->latitude());
                        }
                    });
                }
            }
        
            $mensch_access_instance2 = new CO_Access('tertiary', '', 'CoreysGoryStory');
    
            if ($mensch_access_instance2->valid) {
                $check_list2 = $mensch_access_instance2->generic_search(Array('access_class' => 'CO_LL_Location'), FALSE, 50, 1);
                if (isset($check_list2) && is_array($check_list2) && count($check_list2)) {
                    array_walk($check_list2, function($item, $index) {
                        if (isset($item) && $item && ($item instanceof CO_LL_Location)) {
                            global $result_table;
                            $result_table[$index]['CS2'] = $item->i_can_see_clearly_now() ? '√' : 'X';
                            $result_table[$index]['fuzzed-3'] = Array($item->raw_longitude(), $item->raw_latitude());
                        }
                    });
                }
            }
            
            $setup_list2 = $god_access_instance->generic_search(Array('access_class' => 'CO_LL_Location'), FALSE, 50, 1);
            if (isset($setup_list2) && is_array($setup_list2) && count($setup_list2)) {
                array_walk($setup_list2, function($item, $index) {
                    if (isset($item) && $item && ($item instanceof CO_LL_Location)) {
                        if ($index % 2) {
                            $success = $item->set_fuzz_factor(0);
                        } else {
                            $success = $item->set_can_see_through_the_fuzz(7);
                        }
                    }
                });
        
                $mensch_access_instance3 = new CO_Access('tertiary', '', 'CoreysGoryStory');
        
                if ($mensch_access_instance3->valid) {
                    $check_list3 = $mensch_access_instance3->generic_search(Array('access_class' => 'CO_LL_Location'), FALSE, 50, 1);
                    if (isset($check_list3) && is_array($check_list3) && count($check_list3)) {
                        array_walk($check_list3, function($item, $index) {
                            if (isset($item) && $item && ($item instanceof CO_LL_Location)) {
                                global $result_table;
                                $result_table[$index]['CS3'] = $item->i_can_see_clearly_now() ? '√' : 'X';
                                $result_table[$index]['un-fuzzed'] = Array($item->raw_longitude(), $item->raw_latitude());
                            }
                        });
                    }
                }
            }
        }
        
        echo('<table cellspacing="0" cellpadding="0" border="0" style="font-size:x-small">');
            $keys = array_keys($result_table[0]);
            
            if ($keys) {
                echo('<thead>');
                    echo('<tr style="background-color:black; color:white; margin:0; padding:0.25em; font-weight:bold; text-align:center">');
                        foreach($keys as $key) {
                            echo('<td>'.htmlspecialchars($key).'</td>');
                        }
                    echo('</tr>');
                echo('</thead>');
            }
            
            echo('<tbody>');
                $index = 0;
                foreach($result_table as $row) {
                    echo('<tr>');
                        foreach($row as $col) {
                            echo('<td style="padding:0.25em;border:1px solid black;');
                                if ($index % 2) {
                                    echo('background-color:yellow"');
                                }
                            echo('">');
                            if (is_array($col)) {
                                echo(htmlspecialchars($col[0]).', '.htmlspecialchars($col[1]));
                            } else {
                                echo(htmlspecialchars($col));
                            }
                            echo('</td>');
                        }
                        $index++;
                    echo('</tr>');
                }
            echo('</tbody>');
        echo('</table>');
    }
?>