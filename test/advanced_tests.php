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
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'advanced-item-access-tests\')">COMPLEX GENERIC SEARCH TESTS</a></h1>');
            echo('<div class="container">');
                ?>
                <div class="main_div" style="margin-right:2em">
                    <p class="explain"></p>
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
?>