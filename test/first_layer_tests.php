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
            echo('</div>');
        echo('</div>');
    ?>
</div>
