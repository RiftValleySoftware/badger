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
    $config_file_path = dirname(__FILE__).'/config/s_config.class.php';
    $data_sql_file_path = dirname(__FILE__).'/sql/badger_test_data.sql';
    $security_sql_file_path = dirname(__FILE__).'/sql/badger_test_security.sql';
    
    date_default_timezone_set ( 'UTC' );
    
    if ( !defined('LGV_CONFIG_CATCHER') ) {
        define('LGV_CONFIG_CATCHER', 1);
    }
    
    require_once($config_file_path);
    
    function prepare_databases($in_file_prefix) {
        if ( !defined('LGV_DB_CATCHER') ) {
            define('LGV_DB_CATCHER', 1);
        }

        require_once(CO_Config::db_class_dir().'/co_pdo.class.php');
    
        if ( !defined('LGV_ERROR_CATCHER') ) {
            define('LGV_ERROR_CATCHER', 1);
        }

        require_once(CO_Config::badger_shared_class_dir().'/error.class.php');
        
        $pdo_data_db = NULL;
        try {
            $pdo_data_db = new CO_PDO(CO_Config::$data_db_type, CO_Config::$data_db_host, CO_Config::$data_db_name, CO_Config::$data_db_login, CO_Config::$data_db_password);
        } catch (Exception $exception) {
                    $error = new LGV_Error( 1,
                                            'INITIAL DATABASE SETUP FAILURE',
                                            'FAILED TO INITIALIZE A DATABASE!',
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
                echo('<h1 style="color:red">ERROR WHILE TRYING TO ACCESS DATABASES!</h1>');
                echo('<pre>'.htmlspecialchars(print_r($error, true)).'</pre>');
        }
        
        if ($pdo_data_db) {
            $pdo_security_db = new CO_PDO(CO_Config::$sec_db_type, CO_Config::$sec_db_host, CO_Config::$sec_db_name, CO_Config::$sec_db_login, CO_Config::$sec_db_password);
            
            if ($pdo_security_db) {
                $data_db_sql = file_get_contents(CO_Config::badger_test_class_dir().'/sql/'.$in_file_prefix.'_data.sql');
                $security_db_sql = file_get_contents(CO_Config::badger_test_class_dir().'/sql/'.$in_file_prefix.'_security.sql');
                
                $error = NULL;
        
                try {
                    $pdo_data_db->preparedExec($data_db_sql);
                    $pdo_security_db->preparedExec($security_db_sql);
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
            return;
            }
        }
        echo('');
        echo('<h1 style="color:red">UNABLE TO OPEN DATABASE!</h1>');
    }
    
    function display_record($in_record_object) {
        echo("<h5 style=\"margin-top:0.5em\">ITEM ".$in_record_object->id().":</h5>");
        echo('<div class="inner_div">');
            echo("<p>$in_record_object->class_description</p>");
            echo("<p>$in_record_object->instance_description</p>");
            echo("<p>Read: $in_record_object->read_security_id</p>");
            echo("<p>Write: $in_record_object->write_security_id</p>");
            
            if (method_exists($in_record_object, 'owner_id')) {
                echo("<p>Owner: ".intval($in_record_object->owner_id())."</p>");
            }
            
            if (isset($in_record_object->last_access)) {
                echo("<p>Last access: ".date('g:i:s A, F j, Y', $in_record_object->last_access)."</p>");
            }
            
            if (isset($in_record_object->distance)) {
                $distance = sprintf('%01.3f', $in_record_object->distance);
                echo("<p>Distance: $distance"."Km</p>");
            }
            
            if (method_exists($in_record_object, 'tags')) {
                for ($tagid = 0; $tagid < 10; $tagid++ ) {
                    $tag = NULL;
                    $tags = $in_record_object->tags();
                    if (isset($tags[$tagid])) {
                        $tag = trim($tags[$tagid]);
                        echo("<p>Tag $tagid: \"$tag\"</p>");
                    }
                }
            }
            
            if ( $in_record_object instanceof CO_Security_Login) {
                $ids = $in_record_object->ids();
                if ( isset($ids) && is_array($ids) && count($ids)) {
                    echo("<p>IDs: ");
                        $first = TRUE;
                        foreach ( $ids as $id ) {
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
        
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Honey Badger Don&rsquo;t Care</title>
        <link rel="shortcut icon" href="../icon.png" type="image/png" />
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
                text-align:center;
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
            
            div.test-wrapper {
                display: table;
                margin:auto;
                padding: 0.25em;
                margin-top:1em;
                border-radius:0.5em;
                border:2px solid #999;
                min-width:30em;
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
            
            function expose_tests() {
                var item = document.getElementById('throbber-container');
                
                if (item) {
                    item.style="display:none";
                };
                
                var item = document.getElementById('tests-wrapped-up');
                
                if (item) {
                    item.style="display:block";
                };
            };
            
            window.onload = expose_tests;
        </script>
    </head>
    <body>
        <h1 style="text-align:center">BADGER DATABASE FRAMEWORK TEST</h1>
        <div style="text-align:center;padding:1em;">
            <?php
                if (!isset($_GET['run_tests'])) {
                    if ( !defined('LGV_ACCESS_CATCHER') ) {
                        define('LGV_ACCESS_CATCHER', 1);
                    }
        
                    require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
            ?>
                <img src="../icon.png" style="display:block;margin:auto;width:80px" alt="Honey badger Don&rsquo;t Care" />
                <h1 class="header">MAIN ENVIRONMENT SETUP</h1>
                <div style="text-align:left;margin:auto;display:table">
                    <div class="main_div container">
                        <?php
                            echo("<div style=\"margin:auto;text-align:center;display:table\">");
                            echo("<h2>File/Folder Locations</h2>");
                            echo("<pre style=\"margin:auto;text-align:left;display:table\">");
                            echo("<strong>Badger Version</strong>........".__BADGER_VERSION__."\n");
                            echo("<strong>Base dir</strong>..............".CO_Config::base_dir()."\n");
                            echo("<strong>Main class dir</strong>........".CO_Config::badger_main_class_dir()."\n");
                            echo("<strong>Database class dir</strong>....".CO_Config::db_class_dir()."\n");
                            echo("<strong>Database classes dir</strong>..".CO_Config::db_classes_class_dir()."\n");
                            echo("<strong>Shared class dir</strong>......".CO_Config::badger_shared_class_dir()."\n");
                            echo("<strong>Localization dir</strong>......".CO_Config::badger_lang_class_dir()."\n");
                            echo("<strong>Test class dir</strong>........".CO_Config::badger_test_class_dir()."\n");
                            echo("<strong>Extension classes dir</strong>.".CO_Config::db_classes_extension_class_dir()."\n");
                            echo("</pre></div>");
                        ?>
                        <div class="main_div">
                            <h2 style="text-align:center">Instructions</h2>
                            <p class="explain">In order to run these tests, you should set up two (2) blank databases. They can both be the same DB, but that is not the advised configuration for Badger.</p>
                            <p class="explain">The first (main) database should be called "<?php echo(CO_Config::$data_db_name) ?>", and the second (security) database should be called "<?php echo(CO_Config::$sec_db_name) ?>".</p>
                            <p class="explain">The main database should be have a full rights login named "<?php echo(CO_Config::$data_db_login) ?>", with a password of "<?php echo(CO_Config::$data_db_password) ?>".</p>
                            <p class="explain">The security database should have a full rights login named "<?php echo(CO_Config::$sec_db_login) ?>", with a password of "<?php echo(CO_Config::$sec_db_password) ?>".</p>
                            <p class="explain" style="font-weight:bold;color:red;font-style:italic">This test will wipe out the tables, and set up pre-initialized tables, so it goes without saying that these should be databases (and users) reserved for testing only.</p>
                        </div>
                    </div>
                </div>
                <h3><a href="./?run_tests">RUN THE FUNCTIONAL TESTS</a></h3>
                <h3 style="margin-top:1em"><a href="./mapDemo.php">RUN THE MAP DEMO TEST</a></h3>
            </div>
            <?php } else {
                $start_time = microtime(TRUE);
            ?>
                <div id="throbber-container" style="text-align:center"><img src="images/throbber.gif" alt="throbber" style="position:absolute;width:190px;top:50%;left:50%;margin-top:-95px;margin-left:-95px" /></div>
                <div id="tests-wrapped-up" style="display:none">
                    <img src="../icon.png" style="display:block;margin:auto;width:80px" alt="Honey badger Don't Care" />
                    <div id="basic_tests" class="test-wrapper">
                        <h2>BASIC TESTS</h2>
                        <?php
                        include('basic_tests.php');
                        ?>
                    </div>
                    <div id="first_layer_tests" class="test-wrapper">
                        <h2>FIRST ABSTRACTION LAYER TESTS</h2>
                        <?php
                        include('first_layer_tests.php');
                        ?>
                    </div>
                    <div id="first_layer_tests" class="test-wrapper">
                        <h2>BIG-ASS STRESS TESTS</h2>
                        <?php
                        include('stress_tests.php');
                        ?>
                    </div>
                    <div id="first_layer_tests" class="test-wrapper">
                        <h2>ADVANCED TESTS</h2>
                        <?php
                        include('advanced_tests.php');
                        ?>
                    </div>
                    <?php
                        $end_time = microtime(TRUE);
                        $seconds = $end_time - $start_time;
                        $minutes = intval($seconds / 60.0);
                        $seconds -= floatval($minutes * 60);
                        echo("<h3 style=\"margin-top:1em\">The entire test suite took ".(((0 < $minutes) ? "$minutes minute".((1 < $minutes) ? 's' : ''). ' and ' : '')).sprintf('%01.3f', $seconds)." seconds to run.</h3>");
                    ?>
                    <h3 style="margin-top:1em"><a href="./">RETURN TO MAIN ENVIRONMENT SETUP</a></h3>
                </div>
            <?php } ?>
        </div>
    </body>
</html>

