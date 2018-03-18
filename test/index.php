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
            <div id="basic_tests" class="test-wrapper">
                <h2>BASIC TESTS</h2>
                <?php include('basic_tests.php'); ?>
            </div>
        </div>
    </body>
</html>

