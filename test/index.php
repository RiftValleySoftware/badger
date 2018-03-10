<?php
    $config_file_path = dirname(dirname(__FILE__)).'/config/s_config.class.php';
    
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
        <?php
            echo("<pre>");
            echo("<strong>Base dir</strong>.............".CO_Config::db_class_dir()."\n");
            echo("<strong>Main class dir</strong>.......".CO_Config::main_class_dir()."\n");
            echo("<strong>Database class dir</strong>...".CO_Config::db_class_dir()."\n");
            echo("<strong>Database classes dir</strong>.".CO_Config::db_classes_class_dir()."\n");
            echo("<strong>Shared class dir</strong>.....".CO_Config::shared_class_dir()."\n");
            echo("<strong>Localization dir</strong>.....".CO_Config::lang_class_dir()."\n");
            echo("<strong>Test class dir</strong>.......".CO_Config::test_class_dir()."\n");
            echo("</pre>");
        ?>
        <h1>Operation</h1>
        <h2>Attach Databases</h2>
        <?php
            $access_instance = null;
            
	        if ( !defined('LGV_ACCESS_CATCHER') ) {
                define('LGV_ACCESS_CATCHER', 1);
            }
            
            require_once(CO_Config::main_class_dir().'/co_access.class.php');
            
            $access_instance = new CO_Access();
            
            if ($access_instance->valid) {
                echo("<h3>Both Databases Connected!</h3>");
            } else {
                echo("<h3>ERROR!</h3>");
                echo("<pre>");
                var_dump($access_instance->error);
                echo("</pre>");
            }
        ?>
        <h2>Access Databases</h2>
        <h3>Security Database</h3>
        <?php
            if ($access_instance->valid) {
                $security_db = $access_instance->security_db_object;
                
                if (isset($security_db)) {
                    $test_item = $security_db->get_single_record_by_id(CO_Config::$god_mode_id);
                    
                    echo("<h4>Get God Mode Security Database Item</h4>");
                    if ( isset($test_item) ) {
                        echo("<p>$test_item->class_description</p>");
                        echo("<p>$test_item->instance_description</p>");
                    } else {
                        echo("<h4>NO ITEMS!</h4>");
                    }
                } else {
                    echo("<h4>ERROR!</h4>");
                }
            }
        ?>
        <h3>Main Database</h3>
        <?php
            if ($access_instance->valid) {
                $main_db = $access_instance->data_db_object;
                
                if (isset($main_db)) {
                    $test_item = $main_db->get_single_record_by_id(1);
                    
                    echo("<h4>Get First Main Database Item</h4>");
                    if ( isset($test_item) ) {
                        echo("<p>$test_item->class_description</p>");
                        echo("<p>$test_item->instance_description</p>");
                    } else {
                        echo("<h4>NO ITEMS!</h4>");
                    }
                } else {
                    echo("<h4>ERROR!</h4>");
                }
            }
        ?>
    </body>
</html>
