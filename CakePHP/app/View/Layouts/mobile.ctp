<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
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

            //echo $this->Html->css('cake.generic');

            echo $this->Html->css('mobile');
            echo $this->Html->css('footable/footable.editing.min.css');
            echo $this->Html->css('jquery/jquery.simplecolorpicker');
            echo $this->Html->css('jquery/jquery-ui.min');


            echo $this->fetch('meta');
            echo $this->fetch('css');
            echo $this->fetch('script');
            ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.min.css" rel="stylesheet">

<?php
echo $this->Html->css('mobile');
echo $this->Html->script("jquery/jquery-1.12.2.min");
?>


        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-static-top">
            <div class="navbar-header" style="margin-left:1.1em">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="btn btn-default navbar-btn navbar-left" href="/list">Go back</a>
                <span><span class="navbar-brand" id="list_title"><?php echo $ListTitle ?></span></span>

            </div>
            <div id="navbar" class="navbar-collapse collapse">

                <p class="navbar-text navbar-right" style="margin-right:20px">Signed in as
                        <?php echo $this->Session->read("Auth.User.name") ?>
                </p>
                <a href="/logout">
                    <button type="button" class="btn btn-default navbar-btn navbar-right">Log out</button>
                </a>

            </div>
        </nav>

        <div id="container" class="container">
<?php echo $this->Flash->render(); ?>


            <?php echo $this->fetch('content'); ?>

        </div>

<?php
echo $this->Html->script("footable/footable.core.min");
echo $this->Html->script("footable/footable.editing");
echo $this->Html->script("mobile");
echo $this->Html->script("jquery/jquery.simplecolorpicker");
echo $this->Html->script("jquery/jquery-ui.min");
echo $this->Html->script("bootstrap/bootstrap-multiselect.min.js");
?>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.min.js"></script>

    </body>
</html>
