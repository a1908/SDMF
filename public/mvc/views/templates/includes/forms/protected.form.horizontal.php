<?php
	require PAGE_BLOCKS_PATH."protected.form.horizonal.header.html.php";
?>

<form name="uf" method="post" class="form-horizontal" onsubmit="processForm(this); return false;">

	<div class="row">
		<div class="col-xs-12">
	
	<?php
		foreach($model->getFieldsList(isset($excluded)?$excluded:null) as $fld){
			$model->horizonalFormInputHTML($fld,$update);	
		}
	?>
	<?php
		require PAGE_BLOCKS_PATH."protected.form.horizontal.buttons.html.php";
	?>
	
		</div>
	</div>

</form>

<script src="<?=WEB_ROOT?>/js/processForm.js"></script>
