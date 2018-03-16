<?php
    $config_file_path = dirname(__FILE__).'/config/s_config.class.php';
    $data_sql_file_path = dirname(__FILE__).'/sql/badger_test_data.sql';
    $security_sql_file_path = dirname(__FILE__).'/sql/badger_test_security.sql';
    
    if ( !defined('LGV_CONFIG_CATCHER') ) {
        define('LGV_CONFIG_CATCHER', 1);
    }
    
    require_once($config_file_path);
    
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>DB APP</title>
        <style>
            *{margin:0;padding:0}
            body {
                font-family: Arial, San-serif;
                }
            
            div.main_div {
                margin-top:0.25em;
                margin-bottom: 0.25em;
                margin-left:1em;
                padding: 0.5em;
            }
            
            div.inner_div {
                margin-top:0.25em;
                margin-left:1em;
                padding: 0.25em;
            }
            
            .explain {
                font-style: italic;
            }
            
            h1.header {
                font-size: large;
                margin-top: 1em;
            }
            
            div.open h1.header {
            }
            
            div.closed h1.header {
            }
            
            div.open div.container {
                margin-left:1em;
                display: block;
            }
            
            div.closed div.container {
                display: none;
            }
            
            h2.inner_header {
                font-size: medium;
            }
            
            div.inner_open h2.inner_header {
            }
            
            div.inner_closed h2.inner_header {
            }
            
            div.inner_open div.inner_container {
                margin-left:1em;
                display: block;
                border:1px dashed #555;
            }
            
            div.inner_closed div.inner_container {
                display: none;
            }
            
        </style>
        
        <script type="text/javascript">
            function toggle_main_state(in_id) {
                var item = document.getElementById(in_id);
                
                if ( item.className == 'closed' ) {
                    item.className = 'open';
                } else {
                    item.className = 'closed';
                };
                
            };
            
            function toggle_inner_state(in_id) {
                var item = document.getElementById(in_id);
                
                if ( item.className == 'inner_closed' ) {
                    item.className = 'inner_open';
                } else {
                    item.className = 'inner_closed';
                };
                
            };
        </script>
    </head>
    <body>
        <h1 style="text-align:center">BADGER DATABASE FRAMEWORK TEST</h1>
        <div style="text-align:center;padding:1em;">
            <img src="../icon.png" style="display:block;margin:auto;width:80px" alt="Honey badger Don't Care" />
            <div style="display:table;margin-left:auto;margin-right:auto;text-align:left">
                <div id="initial-setup" class="closed">
                <h1 class="header"><a href="javascript:toggle_main_state('initial-setup')">ENVIRONMENT SETUP</a></h1>
                <div class="main_div container">
                <?php
                    echo("<pre>");
                    echo("<strong>Base dir</strong>.............".CO_Config::base_dir()."\n");
                    echo("<strong>Main class dir</strong>.......".CO_Config::main_class_dir()."\n");
                    echo("<strong>Database class dir</strong>...".CO_Config::db_class_dir()."\n");
                    echo("<strong>Database classes dir</strong>.".CO_Config::db_classes_class_dir()."\n");
                    echo("<strong>Shared class dir</strong>.....".CO_Config::shared_class_dir()."\n");
                    echo("<strong>Localization dir</strong>.....".CO_Config::lang_class_dir()."\n");
                    echo("<strong>Test class dir</strong>.......".CO_Config::test_class_dir()."\n");
                    echo("</pre>");
            
                    prepare_databases();
                ?>
                </div>
                </div>
                <?php
                    echo('<div id="login-tests" class="closed">');
                        echo('<h1 class="header"><a href="javascript:toggle_main_state(\'login-tests\')">LOGIN TESTS</a></h1>');
                        echo('<div class="container">');
                    
                            echo('<div id="test-001" class="inner_closed">');
                                echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-001\')">TEST 1: Try attaching with no logins at all</a></h2>');
    
                                echo('<div class="main_div odd inner_container">');
                                    ?>
                                    <div class="main_div" style="margin-right:2em">
                                    <p class="explain">What is expected here, is that we will have no security database (It won't even be instantiated if there's no login),</p>
                                    <p class="explain">and only the publicly readable data items will be visible (items 1, 7, 8).</p>
                                    <p class="explain">There will be no writable items.</p>
                                    </div>
                                    <?php
                                    try_dbs();
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
                                    try_dbs('secondary', '', 'Ralph');
                                echo('</div>');
                            echo('</div>');
    
                            echo('<div id="test-003" class="inner_closed">');
                                echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-003\')">TEST 3: Try attaching with an invalid login ID ("Fred"), but valid God Mode password ("'.CO_Config::$god_mode_password.'")</a></h2>');
    
                                echo('<div class="main_div inner_container">');
                                    ?>
                                    <div class="main_div" style="margin-right:2em">
                                    <p class="explain">This should flat-out fail.</p>
                                    </div>
                                    <?php
                                    try_dbs('Fred', CO_Config::$god_mode_password);
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
                                    try_dbs('Fred', CO_Config::$god_mode_password);
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
                                    try_dbs('secondary', 'CoreysGoryStory');
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
                                    try_dbs('Fred', 'CodYOzPtwxb4A');
                                echo('</div>');
                            echo('</div>');
    
                            echo('<div id="test-007" class="inner_closed">');
                                echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-007\')">TEST 7: Try attaching with a valid God Mode login ID ("admin"), and valid God Mode password ("'.CO_Config::$god_mode_password.'")</a></h2>');
    
                                echo('<div class="main_div inner_container">');
                                    ?>
                                    <div class="main_div" style="margin-right:2em">
                                    <p class="explain">This ID should have read and write access to every single item in both databases.</p>
                                    </div>
                                    <?php
                                    try_dbs('admin', NULL, CO_Config::$god_mode_password);
                                echo('</div>');
                            echo('</div>');
    
                            echo('<div id="test-008" class="inner_closed">');
                                echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-008\')">TEST 8: Try attaching with a valid secondary login ID ("secondary"), and a valid password ("CoreysGoryStory")</a></h2>');
    
                                echo('<div class="main_div inner_container">');
                                    ?>
                                    <div class="main_div" style="margin-right:2em">
                                    <p class="explain">This ID has access (read and write) only to itself in the security database.</p>
                                    <p class="explain">It has read-write access to items 1, 2, 4, 5, 6, 7, 8 of the main database.</p>
                                    <p class="explain">It has no access to item 3 of the main database.</p>
                                    </div>
                                    <?php
                                    try_dbs('secondary', '', 'CoreysGoryStory');
                                echo('</div>');
                            echo('</div>');
    
                            echo('<div id="test-009" class="inner_closed">');
                                echo('<h2 class="inner_header"><a href="javascript:toggle_inner_state(\'test-009\')">TEST 9: Try attaching with a valid tertiary login ID ("tertiary"), and a valid hashed password ("CodYOzPtwxb4A")</a></h2>');
    
                                echo('<div class="main_div inner_container">');
                                    ?>
                                    <div class="main_div" style="margin-right:2em">
                                    <p class="explain">This ID has access (read and write) only to itself in the security database.</p>
                                    <p class="explain">It has read-write access to items 1, 3, 6, 8 of the main database.</p>
                                    <p class="explain">It has read-only access to items 1, 3, 6, 7, 8 of the main database.</p>
                                    <p class="explain">It has no access to items 2, 4, 5 of the main database.</p>
                                    </div>
                                    <?php
                                    try_dbs('tertiary', 'CodYOzPtwxb4A');
                                echo('</div>');
                            echo('</div>');
                        echo('</div>');
                    echo('</div>');
                ?>
            </div>
        </div>
    </body>
</html>
<?php
    function prepare_databases() {
        if ( !defined('LGV_DB_CATCHER') ) {
            define('LGV_DB_CATCHER', 1);
        }

        require_once(CO_Config::db_class_dir().'/co_pdo.class.php');
    
        if ( !defined('LGV_ERROR_CATCHER') ) {
            define('LGV_ERROR_CATCHER', 1);
        }

        require_once(CO_Config::shared_class_dir().'/error.class.php');
        
        echo('<h1 style="margin-top:1em">Setting Up Initial Database Structure</h1>');
        echo('<div class="main_div">');
        $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
        
        if ($pdo_data_db) {
            $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
            
            if ($pdo_security_db) {
                $data_db_sql = file_get_contents(CO_Config::test_class_dir().'/sql/badger_test_data.sql');
                $security_db_sql = file_get_contents(CO_Config::test_class_dir().'/sql/badger_test_security.sql');
                
                $error = NULL;
        
                try {
                    $pdo_data_db->preparedExec($data_db_sql);
                    echo('<h2>Sucessfully initialized the data DB</h2>');
                    $pdo_security_db->preparedExec($security_db_sql);
                    echo('<h2>Sucessfully initialized the security DB</h2>');
                } catch (Exception $exception) {
                    $error = new LGV_Error( 1,
                                            'INITIAL DATABASE SETUP FAILURE',
                                            'FAILED TO INITIALIZE A DATABASE!',
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
                                                    
                echo('<h1 style="color:red">ERROR WHILE TRYING TO OPEN DATABASES!</h1>');
                echo('<pre>'.htmlspecialchars(print_r($error, true)).'</pre>');
                }
            echo('</div>');
            return;
            }
        }
        echo('</div>');

        echo('<h1 style="color:red">UNABLE TO OPEN DATABASE!</h1>');
    }
    
    function try_dbs($in_login = NULL, $in_hashed_password = NULL, $in_password = NULL) {
        $access_instance = NULL;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
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
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                        echo('</div>');
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
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                        echo('</div>');
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

                for ($id_no = 1; $id_no < 9; $id_no++) {
                    $test_item = $access_instance->get_single_data_record_by_id($id_no);
                    
                    if ($test_item) {
                        echo('<div class="inner_div">');
                            echo("<h4>Get Single Indexed Main Database Item $id_no</h4>");
                            echo('<div class="inner_div">');
                                if ( isset($test_item) ) {
                                    echo("<p>ID: $test_item->id</p>");
                                    echo("<p>$test_item->class_description</p>");
                                    echo("<p>$test_item->instance_description</p>");
                                    echo("<p>Read: $test_item->read_security_id</p>");
                                    echo("<p>Write: $test_item->write_security_id</p>");
                                } else {
                                    echo("<h4>NO ITEM!</h4>");
                                }
                            echo('</div>');
                        echo('</div>');
                    } else {
                        echo("<h4 style=\"color:red;font-weight:bold\">DATA ITEM $id_no IS NOT ACCESSIBLE</h4>");
                    }
                }
            
                $test_item = $access_instance->get_multiple_data_records_by_id(Array(1,3,5,7));
            
                echo('<div class="inner_div">');
                    echo("<h4>Get Multiple Indexed Main Database Items (1, 3, 5, 7)</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                        echo('</div>');
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
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                            if ( $item instanceof CO_Security_Login) {
                                                if ( isset($item->ids) && is_array($item->ids) && count($item->ids)) {
                                                    echo("<p>IDs: ");
                                                        $first = TRUE;
                                                        foreach ( $item->ids as $id ) {
                                                            if (!$first) {
                                                                echo(", ");
                                                            } else {
                                                                $first = FALSE;
                                                            }
                                                            echo($id);
                                                        }
                                                    echo("</p>");
                                                } else {
                                                    echo("<h4>NO IDS!</h4>");
                                                }
                                            }
                                        echo('</div>');
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
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                            if ( $item instanceof CO_Security_Login) {
                                                if ( isset($item->ids) && is_array($item->ids) && count($item->ids)) {
                                                    echo("<p>IDs: ");
                                                        $first = TRUE;
                                                        foreach ( $item->ids as $id ) {
                                                            if (!$first) {
                                                                echo(", ");
                                                            } else {
                                                                $first = FALSE;
                                                            }
                                                            echo($id);
                                                        }
                                                    echo("</p>");
                                                } else {
                                                    echo("<h4>NO IDS!</h4>");
                                                }
                                            }
                                        echo('</div>');
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

                for ($id_no = 1; $id_no < 6; $id_no++) {
                    $test_item = $access_instance->get_single_security_record_by_id($id_no);
            
                    if ($test_item) {
                        echo('<div class="inner_div">');
                            echo("<h4>Get Single Indexed Security Database Item $id_no</h4>");
                            echo('<div class="inner_div">');
                                if ( isset($test_item) ) {
                                    echo("<p>ID: $test_item->id</p>");
                                    echo("<p>$test_item->class_description</p>");
                                    echo("<p>$test_item->instance_description</p>");
                                    echo("<p>Read: $test_item->read_security_id</p>");
                                    echo("<p>Write: $test_item->write_security_id</p>");
                                    if ( $test_item instanceof CO_Security_Login) {
                                        if ( isset($test_item->ids) && is_array($test_item->ids) && count($test_item->ids)) {
                                            echo("<p>IDs: ");
                                                $first = TRUE;
                                                foreach ( $test_item->ids as $id ) {
                                                    if (!$first) {
                                                        echo(", ");
                                                    } else {
                                                        $first = FALSE;
                                                    }
                                                    echo($id);
                                                }
                                            echo("</p>");
                                        } else {
                                            echo("<h4>NO IDS!</h4>");
                                        }
                                    }
                                } else {
                                    echo("<h4>NO ITEM!</h4>");
                                }
                            echo('</div>');
                        echo('</div>');
                    } else {
                        echo("<h4 style=\"color:red;font-weight:bold\">SECURITY ITEM $id_no IS NOT ACCESSIBLE</h4>");
                    }
                }

                $test_item = $access_instance->get_multiple_security_records_by_id(Array(1,2,3,5));
            
                echo('<div class="inner_div">');
                    echo("<h4>Get Multiple Security Database Items (1, 2, 3, 5)</h4>");
                    echo('<div class="inner_div">');
                        if ( isset($test_item) ) {
                            if (is_array($test_item)) {
                                if (count($test_item)) {
                                    foreach ( $test_item as $item ) {
                                        echo("<h5>ITEM $item->id:</h5>");
                                        echo('<div class="inner_div">');
                                            echo("<p>$item->class_description</p>");
                                            echo("<p>$item->instance_description</p>");
                                            echo("<p>Read: $item->read_security_id</p>");
                                            echo("<p>Write: $item->write_security_id</p>");
                                            if ( $item instanceof CO_Security_Login) {
                                                if ( isset($item->ids) && is_array($item->ids) && count($item->ids)) {
                                                    echo("<p>IDs: ");
                                                        $first = TRUE;
                                                        foreach ( $item->ids as $id ) {
                                                            if (!$first) {
                                                                echo(", ");
                                                            } else {
                                                                $first = FALSE;
                                                            }
                                                            echo($id);
                                                        }
                                                    echo("</p>");
                                                } else {
                                                    echo("<h4>NO IDS!</h4>");
                                                }
                                            }
                                        echo('</div>');
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
