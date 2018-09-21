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

            <?php
                echo $this->Html->script("jquery/jquery-1.12.2.min.js");;
        ?>
        <style> 
            .navbar-dark{
                background-color: rgb(107,107,107);
                color: white;
                border-color: #e7e7e7;
            }
            .navbar-dark a{
                color: white;
            }
            
        </style>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>

        <nav class="navbar navbar-dark navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">TD</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                <?php
                                if($this->Session->check("Auth.User")):
                                       ?>
                    <a href="/logout">
                        <button type="button" class="btn btn-default navbar-btn navbar-right">Log out</button>
                    </a>
                    <p class="navbar-text navbar-right" style="margin-right:20px">Signed in as
                        <?php echo $this->Session->read("Auth.User.name") ?>
                    </p>
                    <?php
                                endif;
                        ?>
                </div>
            </div>
        </nav>

        <div id="container" class="container">

        <?php echo $this->Flash->render(); ?>

            <?php echo $this->fetch('content'); ?>
        </div>

    <?php echo $this->Html->script("footable/footable.core.min"); ?>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    </body>

</html>