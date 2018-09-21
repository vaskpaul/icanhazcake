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
           TD - online
        </title>

        <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>

    <body>
        <!--<div id="content">-->
        <?php echo $this->Flash->render(); ?>

        <?php echo $this->fetch('content'); ?>
        <!-- </div>-->

        
    </body>

</html>