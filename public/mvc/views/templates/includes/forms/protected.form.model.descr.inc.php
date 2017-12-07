<form name="uf" method="post" enctype="multipart/form-data" onsubmit="processForm(this); return false;">
<?php
	$model->setSelectSize(10);
?>
	<div class="row" id="description">
		<div class="col-xs-6">
<?php
			$model->basicFormInputHTML("title",$update);
			$model->basicFormInputHTML("furniture_type_id",$update);
?>	
			<div class="row">
				<div class="col-xs-6">
<?php
					$model->basicFormInputHTML("weight",$update);
?>
				</div>
				<div class="col-xs-6">
<?php
					$model->basicFormInputHTML("cubage",$update);
?>	
				</div>
			</div>
<?php
			$model->basicFormInputHTML("descr",$update);
?>
		</div>
		<div class="col-xs-6">
			<div class="row">
				<div class="col-xs-12">
<?php
					$model->basicFormInputHTML("manufacturer_id",$update);
					$model->basicFormInputHTML("designer",$update);
?>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-xs-6">
<?php
					$model->basicFormInputHTML("catalogue",$update);
?>	
				</div>
				<div class="col-xs-6">
<?php
					$model->basicFormInputHTML("style",$update);
?>	
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<button type="submit" class="btn btn-primary" onclick="$('form[name=uf]').submit()"><?=__("button-save")?></button>
				<button type="reset" class="btn btn-danger"><?=__("button-reset")?></button>
			</div>
		</div>
	</div>
</form>
<script src="<?=WEB_ROOT?>/js/processForm.js"></script>
