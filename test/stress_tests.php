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
        prepare_databases('stress_test');
        echo('<div id="stress-item-access-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'stress-item-access-tests\')">ACCESS ITEMS TEST</a></h1>');
            echo('<div class="container">');
                ?>
                <div class="main_div" style="margin-right:2em">
                    <p class="explain"></p>
                </div>
                <?php
                echo('<div id="test-020" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-020\')">TEST 20: Get All Readable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We don't log in (can only see publicly viewable items), and ask to see all readable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        try_get_all_readable_items_stress();
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-021" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-021\')">TEST 21: Get All Writeable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We don't log in (can only see publicly writeable items), and ask to see all writeable items.</p>
                        <p class="explain">We expect this to return no items.</p>
                        </div>
                        <?php
                        try_get_all_writeable_items_stress();
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-022" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-022\')">TEST 22: Log in With A Secondary Login, and Get All Readable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, and ask to see all readable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        try_get_all_readable_items_stress('secondary', '', 'CoreysGoryStory');
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-023" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-023\')">TEST 23: Log in With A Secondary Login, and Get All Writeable Items.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">We log in, and ask to see all writeable items.</p>
                        <p class="explain">We expect this to return many items.</p>
                        </div>
                        <?php
                        try_get_all_writeable_items_stress('secondary', '', 'CoreysGoryStory');
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php
    function try_get_all_readable_items_stress($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
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
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $test_item = $access_instance->get_all_data_writeable_records();
        
            echo('<div class="inner_div">');
                echo("<h4>Get All Writeable Main Database Items</h4>");
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
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
?>