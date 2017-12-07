<!DOCTYPE HTML>
<html>
<head>
<?php
	require PAGE_BLOCKS_PATH."default.head.php";
?>
</head>
<?php
	require PAGE_BLOCKS_PATH."default.js.php";
?>

<body>
	
<section class="header">	
<?php require PAGE_BLOCKS_PATH."default.header.html.php"; ?>
</section>


<section class="content">
<?php if( Session::hasFlash() ){?>

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<div class="alert alert-info">
				<?php Session::flash(); ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?=$content?>
</section>

<section class="footer">
<?php require PAGE_BLOCKS_PATH."default.footer.html.php"; ?>
</section>		

</body>
</html>
