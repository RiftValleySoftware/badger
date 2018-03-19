<div style="display:table;margin-left:auto;margin-right:auto;text-align:left">
    <div id="first-layer-initial-setup" class="closed">
        <h1 class="header"><a href="javascript:toggle_main_state('first-layer-initial-setup')">ENVIRONMENT SETUP</a></h1>
        <div class="main_div container">
            <?php
                prepare_databases('first_layer_test');
            ?>
        </div>
    </div>
    <?php
        echo('<div id="item-access-tests" class="closed">');
            echo('<h1 class="header"><a href="javascript:toggle_main_state(\'item-access-tests\')">ACCESS ITEMS TEST</a></h1>');
            echo('<div class="container">');
                echo('<div id="test-013" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-013\')">TEST 13: Try attaching with no logins at all, and try to modify the Las Vegas record.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a public anonymous member, and then tries to modify a record with a '0' mod level (can be modified by any logged-in member).</p>
                        <p class="explain">We expect this to fail.</p>
                        </div>
                        <?php
                        try_change_record(NULL, NULL, NULL, 2);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-014" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-014\')">TEST 14: Try attaching with the secondary login, and modify the Las Vegas record.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a secondary member, and then tries to modify a record with a '0' mod level (can be modified by any logged-in member).</p>
                        <p class="explain">We expect this to succeed.</p>
                        </div>
                        <?php
                        try_change_record('secondary', '', 'CoreysGoryStory', 2);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-015" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-015\')">TEST 15: Try attaching with the secondary login, and modify the NA World Services record.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a secondary member, and then tries to modify a record that is not on the "guest list."</p>
                        <p class="explain">We expect this to fail completely, as we do not even have read access.</p>
                        </div>
                        <?php
                        try_change_record('secondary', '', 'CoreysGoryStory', 4);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-016" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-016\')">TEST 16: Try attaching with the tertiary login, and modify the San Jose (Costa Rica) record.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a tertiary member, and then tries to modify a record that is readable, but not writeable.</p>
                        <p class="explain">We expect this to fail, as we do not have write access.</p>
                        </div>
                        <?php
                        try_change_record('tertiary', 'CodYOzPtwxb4A', '', 8);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-017" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-017\')">TEST 17: Make a new, blank record, then modify it.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a tertiary member, and then creates a new record. The ID of the record should be ten.</p>
                        <p class="explain">After we create the new record, we use its accessors to set some new values.</p>
                        <p class="explain">We expect this to succeed.</p>
                        </div>
                        <?php
                        try_make_new_data_record('tertiary', 'CodYOzPtwxb4A', '', 8);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-018" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-018\')">TEST 18: Delete the Record Formerly Known as Las Vegas.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a tertiary member, and then deletes the second record (the one that used to be "Las Vegas" before we abused it).</p>
                        <p class="explain">We expect this to succeed.</p>
                        </div>
                        <?php
                        try_delete_old_data_record('tertiary', 'CodYOzPtwxb4A', '', 2);
                    echo('</div>');
                echo('</div>');
                echo('<div id="test-019" class="inner_closed">');
                    echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-019\')">TEST 19: Try to delete a record we have no write privileges to.</a></h2>');

                    echo('<div class="main_div inner_container">');
                        ?>
                        <div class="main_div" style="margin-right:2em">
                        <p class="explain">This test accesses as a tertiary member, and then deletes the eighth record (we have read, but no write).</p>
                        <p class="explain">We expect this to fail.</p>
                        </div>
                        <?php
                        try_delete_old_data_record('tertiary', 'CodYOzPtwxb4A', '', 8);
                    echo('</div>');
                echo('</div>');
            echo('</div>');
        echo('</div>');
    ?>
</div>
<?php
    function try_delete_old_data_record($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL, $in_record_id = 2) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $test_item = $access_instance->get_single_data_record_by_id($in_record_id);
            if ( isset($test_item) ) {
                display_record($test_item);
                $test_item->delete_from_db();
                $test_item = $access_instance->get_single_data_record_by_id($in_record_id);
                if ( isset($test_item) ) {
                    echo("<h4 style=\"color:red;font-weight:bold\">ERROR! This Should Not Exist!</h4>");
                    display_record($test_item);
                } else {
                    echo("<h4>Suscess! Yes, We have no bananas!</h4>");
                }
            } else {
                echo("<h4>NO ITEM!</h4>");
            }
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_make_new_data_record($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $test_item = $access_instance->make_new_blank_record('CO_LL_Location');
            if ($test_item) {
                if (10 == $test_item->id()) {
                    echo("<h4>BEFORE:</h4>");
                    echo('<div class="main_div">');
                        display_record($test_item);
                    echo('</div>');
                    if ($test_item->set_longitude(0.1870)) {
                        if ($test_item->set_latitude(5.6037)) {
                            if ($test_item->set_name('Accra, Ghana')) {
                                echo("<h4>AFTER:</h4>");
                                echo('<div class="main_div">');
                                    display_record($test_item);
                                echo('</div>');
                            } else {
                                echo("<h4 style=\"color:red;font-weight:bold\">Could not set name!</h4>");
                                if ($test_item->error) {
                                    echo('<h4 style="color:red;font-weight:bold">ERROR!</h4>');
                                    echo('<pre style="color:red">');
                                        var_dump($test_item->error);
                                    echo("</pre>");
                                }
                            }
                        } else {
                            echo("<h4 style=\"color:red;font-weight:bold\">Could not set latitude!</h4>");
                            if ($test_item->error) {
                                echo('<h4 style="color:red;font-weight:bold">ERROR!</h4>');
                                echo('<pre style="color:red">');
                                    var_dump($test_item->error);
                                echo("</pre>");
                            }
                        }
                    } else {
                        echo("<h4 style=\"color:red;font-weight:bold\">Could not set longitude!</h4>");
                        if ($test_item->error) {
                            echo('<h4 style="color:red;font-weight:bold">ERROR!</h4>');
                            echo('<pre style="color:red">');
                                var_dump($test_item->error);
                            echo("</pre>");
                        }
                    }
                } else {
                    echo("<h4 style=\"color:red;font-weight:bold\">".$test_item->id()." is the wrong ID number!</h4>");
                }
            } else {
                echo("<h4>NO ITEM!</h4>");
            }
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_change_record($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL, $in_record_id = 2) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_hashed_password, $in_password);
        
        if ($access_instance->valid) {
            echo("<h2>The access instance is valid!</h2>");
            $test_item = $access_instance->get_single_data_record_by_id($in_record_id);
            if ( isset($test_item) ) {
                display_record($test_item);
                try_to_change_this_record($test_item, $access_instance);
            } else {
                echo("<h4>NO ITEM!</h4>");
            }
        } else {
            echo("<h2 style=\"color:red;font-weight:bold\">The access instance is not valid!</h2>");
            echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_to_change_this_record( $test_item,
                                        $access_instance
                                        ) {
        if ($test_item instanceof CO_LL_Location) {
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
        
            $result = $test_item->set_name('This Is A New Name');
        
            if (!$result) {
                echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM ".$test_item->id()." DID NOT CHANGE ITS NAME</h4>");
                if ($test_item->error) {
                    echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
                    echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$test_item->error->error_code.') '.$test_item->error->error_name.' ('.$test_item->error->error_description.')</p>');
                }
            }
        
            $result = $test_item->set_tag(0, 'This Is A New Tag');
        
            if (!$result) {
                echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM ".$test_item->id()." DID NOT CHANGE ITS TAG</h4>");
                if ($test_item->error) {
                    echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
                    echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$test_item->error->error_code.') '.$test_item->error->error_name.' ('.$test_item->error->error_description.')</p>');
                }
            }
        
            $result = $test_item->set_tag(8, 'This Is Another New Tag');
        
            if (!$result) {
                echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM ".$test_item->id()." DID NOT CHANGE ITS TAG</h4>");
                if ($test_item->error) {
                    echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
                    echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$test_item->error->error_code.') '.$test_item->error->error_name.' ('.$test_item->error->error_description.')</p>');
                }
            }
        
            if ($test_item->error) {
                echo("<h2 style=\"color:red;font-weight:bold\">Write Error!</h2>");
                echo('<p style="margin-left:1em;color:red;font-weight:bold">Error: ('.$test_item->error->error_code.') '.$test_item->error->error_name.' ('.$test_item->error->error_description.')</p>');
            } else {
                if ($test_item->reload_from_db()) {
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
                    echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM ".$test_item->id()." IS NOT ACCESSIBLE</h4>");
                }
            }
        } else {
            echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM IS NOT ACCESSIBLE</h4>");
        }
    }
?>