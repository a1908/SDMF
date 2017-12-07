<!DOCTYPE html>
<html>
<head>
<?php
	require PAGE_BLOCKS_PATH."protected.head.php";
?>
</head>
<?php
	require PAGE_BLOCKS_PATH."protected.js.php";
	$router = App::getRouter();
?>

<body>
	
<section id="header">
    <!-- Navigation -->
    <nav class="navbar navbar-default" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-main">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?=$router->getWebRoot()?>" class="navbar-brand"><?=__("Admin Panel")?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar-main">
<?php
	$m = new Menu($router->getActiveLink(),"main_menu", $router->getLanguagePath());
	echo $m->html();
?>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
	
	
</section>
	
<section>	
    <div class="container">
		<?php if( Session::hasFlash() ){?>
		<div class="alert alert-info">
			<?php Session::flash(); ?>
		</div>
		<?php } ?>
		<?=$content?>
    </div><!-- /.container -->
</section>

</body>
</html>
