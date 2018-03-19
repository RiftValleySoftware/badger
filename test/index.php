<?php
    $config_file_path = dirname(__FILE__).'/config/s_config.class.php';
    $data_sql_file_path = dirname(__FILE__).'/sql/badger_test_data.sql';
    $security_sql_file_path = dirname(__FILE__).'/sql/badger_test_security.sql';
    
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

        require_once(CO_Config::shared_class_dir().'/error.class.php');
        
        echo('<h1 style="margin-top:1em">Setting Up Initial Database Structure</h1>');
        echo('<div class="main_div">');
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
                $data_db_sql = file_get_contents(CO_Config::test_class_dir().'/sql/'.$in_file_prefix.'_data.sql');
                $security_db_sql = file_get_contents(CO_Config::test_class_dir().'/sql/'.$in_file_prefix.'_security.sql');
                
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
    
    function display_record($in_record_object) {
        echo("<h5 style=\"margin-top:0.5em\">ITEM ".$in_record_object->id().":</h5>");
        echo('<div class="inner_div">');
            echo("<p>$in_record_object->class_description</p>");
            echo("<p>$in_record_object->instance_description</p>");
            echo("<p>Read: $in_record_object->read_security_id</p>");
            echo("<p>Write: $in_record_object->write_security_id</p>");
            
            if (isset($in_record_object->ttl)) {
                $color = "green";
                $seconds = $in_record_object->seconds_remaining_to_live();
                
                if (0 > $seconds) {
                    $color = "red";
                } elseif ((60 * 60 * 24) > $seconds) {
                    $color = "orange";
                }
                
                echo("<p style=\"color:$color\">Seconds Remaining to Live: ".$seconds."</p>");
            }
            
            if (isset($in_record_object->last_access)) {
                echo("<p>Last access: ".date('g:i:s A, F j, Y', $in_record_object->last_access)."</p>");
            }
            
            for ($tagid = 0; $tagid < 10; $tagid++ ) {
                $tag = NULL;
                if (isset($in_record_object->tags[$tagid])) {
                    $tag = trim($in_record_object->tags[$tagid]);
                    echo("<p>Tag $tagid: \"$tag\"</p>");
                }
            }
            
            if ( $in_record_object instanceof CO_Security_Login) {
                if ( isset($in_record_object->ids) && is_array($in_record_object->ids) && count($in_record_object->ids)) {
                    echo("<p>IDs: ");
                        $first = TRUE;
                        foreach ( $in_record_object->ids as $id ) {
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
                <?php
                    if (!isset($_GET['run_tests'])) {
                ?>
                <h1 class="header">MAIN ENVIRONMENT SETUP</h1>
                <div style="text-align:left;margin:auto;display:table">
                    <div class="main_div container">
                        <?php
                            echo("<div style=\"margin:auto;text-align:center;display:table\">");
                            echo("<h2>File/Folder Locations</h2>");
                            echo("<pre style=\"margin:auto;text-align:left;display:table\">");
                            echo("<strong>Base dir</strong>.............".CO_Config::base_dir()."\n");
                            echo("<strong>Main class dir</strong>.......".CO_Config::main_class_dir()."\n");
                            echo("<strong>Database class dir</strong>...".CO_Config::db_class_dir()."\n");
                            echo("<strong>Database classes dir</strong>.".CO_Config::db_classes_class_dir()."\n");
                            echo("<strong>Shared class dir</strong>.....".CO_Config::shared_class_dir()."\n");
                            echo("<strong>Localization dir</strong>.....".CO_Config::lang_class_dir()."\n");
                            echo("<strong>Test class dir</strong>.......".CO_Config::test_class_dir()."\n");
                            echo("</pre></div>");
                        ?>
                        <div class="main_div">
                            <h2 style="text-align:center">Instructions</h2>
                            <p class="explain">In order to run these tests, you should set up two (2) blank databases. They can both be the same DB, but that is not the advised configuration for Badger.</p>
                            <p class="explain">The first (main) database should be called "<?php echo(CO_Config::$data_db_name) ?>", and the second (security) database should be called "<?php echo(CO_Config::$sec_db_name) ?>".</p>
                            <p class="explain">The main database should be have a full rights login named "<?php echo(CO_Config::$data_db_login) ?>", with a password of "<?php echo(CO_Config::$data_db_password) ?>".</p>
                            <p class="explain">The security database should be have a full rights login named "<?php echo(CO_Config::$sec_db_login) ?>", with a password of "<?php echo(CO_Config::$sec_db_password) ?>".</p>
                            <p class="explain" style="font-weight:bold;color:red;font-style:italic">This test will wipe out the tables, and set up pre-initialized tables, so it goes without saying that these should be databases (and users) reserved for testing only.</p>
                        </div>
                    </div>
                </div>
                <h3><a href="./?run_tests">RUN THE TESTS</a></h3>
            </div>
            <?php } else { ?>
                <div id="basic_tests" class="test-wrapper">
                    <h2>BASIC TESTS</h2>
                    <?php include('basic_tests.php'); ?>
                </div>
                <div id="first_layer_tests" class="test-wrapper">
                    <h2>FIRST ABSTRACTION LAYER TESTS</h2>
                    <?php include('first_layer_tests.php'); ?>
                </div>
                <h3 style="margin-top:1em"><a href="./">RETURN TO MAIN ENVIRONMENT SETUP</a></h3>
            <?php } ?>
        </div>
    </body>
</html>

