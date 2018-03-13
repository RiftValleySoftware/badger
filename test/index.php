<?php
    $config_file_path = dirname(__FILE__).'/config/s_config.class.php';
    
    if ( !defined('LGV_CONFIG_CATCHER') ) {
        define('LGV_CONFIG_CATCHER', 1);
    }
    
    require_once($config_file_path);

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>DB APP</title>
    </head>
    <body>
        <h1>Initial Setup</h1>
        <div style="margin-left:1em">
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
            
        ?>
        </div>
        <h1>Operation</h1>
        <div style="margin-left:1em">
            <h1>Initial Setup</h1>
            <div style="margin-left:1em">
                <?php
                    echo('<h3>First, Try attaching with no logins at all</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs();
                    echo('</div>');
            
                    echo('<h3>Next, Try attaching with a valid login ID, but invalid password</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs('secondary', 'Ralph');
                    echo('</div>');
            
                    echo('<h3>Next, Try attaching with an invalid login ID, but valid password</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs('Fred', CO_Config::$god_mode_password);
                    echo('</div>');
            
                    echo('<h3>Next, Try attaching with a valid God mode login ID, and a valid password</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs('admin', CO_Config::$god_mode_password);
                    echo('</div>');
            
                    echo('<h3>Next, Try attaching with a valid secondary login ID, and a valid password</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs('secondary', 'CoreysGoryStory');
                    echo('</div>');
            
                    echo('<h3>Next, Try attaching with a valid tertiary login ID, and a valid password</h3>');
            
                    echo('<div style="margin-left:1em">');
                    try_dbs('tertiary', 'CoreysGoryStory');
                    echo('</div>');
                ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
    function try_dbs($in_login = null, $in_password = null) {
        $access_instance = null;
        
        if ( !defined('LGV_ACCESS_CATCHER') ) {
            define('LGV_ACCESS_CATCHER', 1);
        }
        
        require_once(CO_Config::main_class_dir().'/co_access.class.php');
        
        $access_instance = new CO_Access($in_login, $in_password);
        
        if ($access_instance->valid) {
            echo("<h3>The access instance is valid!</h3>");
            try_security_items($access_instance);
            try_data_items($access_instance);
        } else {
            echo("<h3>The access instance is not valid!</h3>");
            echo('<p style="margin-left:1em">Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
    
    function try_data_items($access_instance) {
        if ($access_instance->main_db_available()) {
            echo("<h4>We have a main DB</h4>");
            echo('<div style="margin-left:1em">');
                for ($id_no = 1; $id_no < 9; $id_no++) {
                    $test_item = $access_instance->get_single_data_record_by_id($id_no);
            
                    echo("<h4>Get Main Database Item $id_no</h4>");
                    echo('<div style="margin-left:1em">');
                        if ( isset($test_item) ) {
                            echo("<p>$test_item->class_description</p>");
                            echo("<p>$test_item->instance_description</p>");
                            echo("<p>Read: $test_item->read_security_id</p>");
                            echo("<p>Write: $test_item->write_security_id</p>");
                        } else {
                            echo("<h4>NO ITEM!</h4>");
                        }
                    echo('</div>');
                }
            
                $test_item = $access_instance->get_multiple_data_records_by_id(Array(1,2,3,4,5));
            
                echo("<h4>Get Multiple Main Database Items</h4>");
                echo('<div style="margin-left:1em">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                foreach ( $test_item as $item ) {
                                    echo("<h5>ITEM:</h5>");
                                    echo('<div style="margin-left:1em">');
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
                            echo("<h4>NO ARRAY!</h4>");
                        }
                    } else {
                        echo("<h4>NOTHING RETURNED!</h4>");
                    }
                echo('</div>');
            echo('</div>');
        } else {
            echo('<h4 style="margin-left:1em">We do not have a main DB</h4>');
        }
    }
    
    function try_security_items($access_instance) {
        if ($access_instance->security_db_available()) {
            echo("<h4>We have a security DB</h4>");
            
            echo('<div style="margin-left:1em">');
                for ($id_no = 1; $id_no < 9; $id_no++) {
                    $test_item = $access_instance->get_single_security_record_by_id($id_no);
            
                    echo("<h4>Get Security Database Item $id_no</h4>");
                    echo('<div style="margin-left:1em">');
                        if ( isset($test_item) ) {
                            echo("<p>$test_item->class_description</p>");
                            echo("<p>$test_item->instance_description</p>");
                            echo("<p>Read: $test_item->read_security_id</p>");
                            echo("<p>Write: $test_item->write_security_id</p>");
                            if ( $test_item instanceof CO_Security_Login) {
                                if ( isset($test_item->ids) && is_array($test_item->ids) && count($test_item->ids)) {
                                    echo("<p>IDs: ");
                                        $first = true;
                                        foreach ( $test_item->ids as $id ) {
                                            if (!$first) {
                                                echo(", ");
                                            } else {
                                                $first = false;
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
                }

                $test_item = $access_instance->get_multiple_security_records_by_id(Array(1,2,3,4,5,6,7,8));
            
                echo("<h4>Get Multiple Security Database Items</h4>");
                echo('<div style="margin-left:1em">');
                    if ( isset($test_item) ) {
                        if (is_array($test_item)) {
                            if (count($test_item)) {
                                foreach ( $test_item as $item ) {
                                    echo("<h5>ITEM:</h5>");
                                    echo('<div style="margin-left:1em">');
                                        echo("<p>$item->class_description</p>");
                                        echo("<p>$item->instance_description</p>");
                                        echo("<p>Read: $item->read_security_id</p>");
                                        echo("<p>Write: $item->write_security_id</p>");
                                        if ( $item instanceof CO_Security_Login) {
                                            if ( isset($item->ids) && is_array($item->ids) && count($item->ids)) {
                                                echo("<p>IDs: ");
                                                    $first = true;
                                                    foreach ( $item->ids as $id ) {
                                                        if (!$first) {
                                                            echo(", ");
                                                        } else {
                                                            $first = false;
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
                            echo("<h4>NO ARRAY!</h4>");
                        }
                    } else {
                        echo("<h4>NOTHING RETURNED!</h4>");
                    }
                echo('</div>');
            echo('</div>');
        } else {
            echo('<h4 style="margin-left:1em">We do not have a security DB</h4>');
        }
    }
?>
