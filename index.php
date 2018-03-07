<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>DB APP</title>
    </head>
    <body>
        <h1>Initial Setup</h1>
        <?php
	        if ( !defined('LGV_CONFIG_CATCHER') ) {
                define('LGV_CONFIG_CATCHER', 1);
            }
            require_once(dirname(__FILE__).'/config/s_config.class.php');
            echo("<pre><strong>Database class dir</strong>...".CO_Config::db_class_dir()."\n");
            echo("<strong>Main class dir</strong>.......".CO_Config::main_class_dir()."\n");
            echo("<strong>Shared class dir</strong>.....".CO_Config::shared_class_dir()."\n");
            echo("<strong>Test class dir</strong>.......".CO_Config::test_class_dir()."</pre>");
        ?>
        <h1>Attach Databases</h1>
        <?php
            $access_instance = null;
            
	        if ( !defined('LGV_ACCESS_CATCHER') ) {
                define('LGV_ACCESS_CATCHER', 1);
            }
            
            require_once(CO_Config::main_class_dir().'/co_access.class.php');
            
            $access_instance = new CO_Access();
        ?>
    </body>
</html>
