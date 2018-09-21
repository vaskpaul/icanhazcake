<?php
/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>

    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $this->fetch('title'); ?>
        </title>

        <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->css('jquery/jquery.simplecolorpicker');
        echo $this->Html->css('jquery/jquery-ui.min');
        ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


        <?php echo $this->Html->css('styles'); ?> 

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>

    <body>
        <div class="fixed">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="navbar-header" style="margin-left:1.1em">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <button type="button" class="btn btn-default navbar-btn" id="saveGoBack" style="float:left">Save & Go back</button>
                    <!--<button class="btn btn-default navbar-btn" id="guardar">GUARDAR</button>-->
                    <span><span class="navbar-brand" id="list_title"><?php echo $ListTitle ?></span></span>
                    <p class="navbar-text">Last modification: <span id="lastmod"><?php echo $lastMod ?></span></p>
                </div>

                <div class="nav navbar-nav navbar-right">
                    <a href="/logout">
                        <button type="button" class="btn btn-default navbar-btn navbar-right">Log out</button>
                    </a>
                    <p class="navbar-text" style="margin-right:20px">Signed in as
                        <?php echo $this->Session->read("Auth.User.name") ?>
                    </p>
                </div>

            </nav>

            <div class="buttons-row row">

                <div class="col-md-12 col-lg-4">
                    <button id="addRow" class="btn btn-success">Add task</button>

                    <div class="btn-group" role="group" aria-label="...">
                        <button id="open_status" class="btn btn-default">Status</button>
                        <button id="open_categories" class="btn btn-default">Categories</button>
                        <button id="open_tags" class="btn btn-default">Tags</button>
                    </div>
                </div>
                <div class="col-md-12 col-lg-8">
                    <select id="filter_status" multiple="multiple">
                    </select>

                    <select id="filter_categories" multiple="multiple">
                    </select>

                    <select id="filter_tags" multiple="multiple">
                    </select>

                    <div class="btn-group" data-toggle="buttons">

                        <label class="btn btn-default">
                            <input type="checkbox" id="hide_completed" > hide 100%
                        </label>
                    </div>

                    <button class="btn btn-primary" id="filter_btn">Filter</button>
                    <button class="btn btn-default" id="reset_filter">Reset</button>

                    <p id="displayed_rows" class="hidden-xs hidden-sm">Showing <span id="showing_rows"></span> / <span id="total_rows"></span></p>
                </div>
            </div>

        </div>
    </div>
    <!--<div id="content">-->
    <?php echo $this->Flash->render(); ?>

    <?php echo $this->fetch('content'); ?>
    <!-- </div>-->


    <?php
    echo $this->Html->script("jquery/jquery-1.12.2.min.js");
    echo $this->Html->script("main");
    echo $this->Html->script("filtering");
    echo $this->Html->script("sorting");
    echo $this->Html->script("interaction_table");
    echo $this->Html->script("jquery/jquery-ui.min");
    echo $this->Html->script("jquery/jquery.simplecolorpicker");
    echo $this->Html->script("bootstrap/bootstrap-multiselect.min.js");
    echo $this->Html->script("bootstrap/bootstrap3-typeahead.min");
    echo $this->Html->script("jquery/jquery.stickytableheaders.min");
    ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.min.js"></script>
</body>

</html>