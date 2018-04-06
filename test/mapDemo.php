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
    set_time_limit ( 120 );
    
    if ( !defined('LGV_CONFIG_CATCHER') ) {
        define('LGV_CONFIG_CATCHER', 1);
    }
    
    require_once($config_file_path);
    
    if ( !defined('LGV_ACCESS_CATCHER') ) {
        define('LGV_ACCESS_CATCHER', 1);
    }

    require_once(CO_Config::badger_main_class_dir().'/co_access.class.php');
    
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
    
    if (isset($_GET['resolve_query'])) {
        $access_instance = new CO_Access();

        echo('[');
        if ($access_instance->valid) {
            list($long, $lat, $radius) = array_map(function($in){ return floatval($in); }, explode(',', trim($_GET['resolve_query'])));
            $test_item = $access_instance->generic_search(Array('location' => Array('longitude' => $long, 'latitude' => $lat, 'radius' => $radius)));
            if (isset($test_item) && is_array($test_item) && count($test_item)) {
                $test = array_map(function($item){return '{"id":'.intval($item->id()).',"name":'.json_encode($item->name).',"longitude":'.floatval($item->longitude()).',"latitude":'.floatval($item->latitude()).',"distance":'.floatval($item->distance).',"weekday":'.intval($item->tags()[7]).'}';}, $test_item);
                echo(implode(',',$test));
            }
        }
        echo(']');
        exit();
    } elseif (isset($_GET['loadDB'])) {
        prepare_databases('stress_test');    
        exit();
    } else {
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="shortcut icon" href="../icon.png" type="image/png" />
        <title>Badger Map Demo</title>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC5meofXldluts0UPGp6Zg234-U989u1pY&libraries=geometry"></script>
        <script type="text/javascript" src="mapDemo.js"></script>
        <style>
            *{margin:0;padding:0}
            html, body, .map_div {
                width: 100%;
                height: 100%;
            }
            body {
                font-family: Arial, San-serif;
            }
            div.centerControlDiv {
                background-color: white;
                padding: 0.25em;
            }
        </style>
    </head>
    <body>
        <div style="width:100%;height:100%">
            <div id="map-container" class="map_div" style="position:absolute;width:100%;height:100%;top:0px;left:0px;z-axis:20000"></div>
            <img id="throbber-container" src="images/throbber.gif" alt="throbber" style="z-axis:20001;position:absolute;width:190px;top:50%;left:50%;margin-top:-95px;margin-left:-95px" />
            <?php
            $access_instance = NULL;
    
            $access_instance = new CO_Access();
    
            if ($access_instance->valid) {
                echo('<script type="text/javascript">new loadTestMap()</script>');
            }
            ?>
        </div>
    </body>
</html>
<?php
}
?>