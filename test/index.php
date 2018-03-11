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
        <h2>Attach Databases (no Login)</h2>
        <?php
            echo('<h3>First, Try attaching with no logins at all</h3>');
            
            try_dbs();
            
            echo('<h3>Next, Try attaching with a valid login ID, but invalid password</h3>');
            
            try_dbs('admin', 'Ralph');
            
            echo('<h3>Next, Try attaching with an invalid login ID, but valid password</h3>');
            
            try_dbs('Fred', CO_Config::$god_mode_password);
            
            echo('<h3>Next, Try attaching with a valid God mode login ID, and a valid password</h3>');
            
            try_dbs('admin', CO_Config::$god_mode_password);
            
            echo('<h3>Next, Try attaching with a valid secondary login ID, and a valid password</h3>');
            
            try_dbs('secondary', 'CoreysGoryStory');
        ?>
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
        
            if ($access_instance->security_db_available()) {
                echo("<h4>We have a security DB</h4>");
                $test_item = $access_instance->get_single_security_record_by_id(CO_Config::$god_mode_id);
                
                echo("<h4>Get God Mode Security Database Item</h4>");
                if ( isset($test_item) ) {
                    echo("<p>$test_item->class_description</p>");
                    echo("<p>$test_item->instance_description</p>");
                } else {
                    echo("<h4>NO ITEM</h4>");
                }
                
                $test_item = $access_instance->get_single_security_record_by_id(2);
                echo("<h4>Get Secondary Security Database Item</h4>");
                if ( isset($test_item) ) {
                    echo("<p>$test_item->class_description</p>");
                    echo("<p>$test_item->instance_description</p>");
                } else {
                    echo("<h4>NO ITEM</h4>");
                }
            } else {
                echo("<h4>We do not have a security DB</h4>");
            }
        
            if ($access_instance->main_db_available()) {
                echo("<h4>We have a main DB</h4>");
                $test_item = $access_instance->get_single_data_record_by_id(1);
                
                echo("<h4>Get First Main Database Item</h4>");
                if ( isset($test_item) ) {
                    echo("<p>$test_item->class_description</p>");
                    echo("<p>$test_item->instance_description</p>");
                } else {
                    echo("<h4>NO ITEMS!</h4>");
                }
            } else {
                echo("<h4>We do not have a main DB</h4>");
            }
        } else {
            echo("<h3>The access instance is not valid!</h3>");
            echo('<p>Error: ('.$access_instance->error->error_code.') '.$access_instance->error->error_name.' ('.$access_instance->error->error_description.')</p>');
        }
    }
?>
