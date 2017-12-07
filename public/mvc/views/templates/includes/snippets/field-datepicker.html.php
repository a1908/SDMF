<div>
	<div class="input-group date"  id="<?=$id1?>">
		<input type="text" class="form-control" value="<?=$display_date?>"<?=isset($placeholder)?" placeholder='$placeholder'":''?><?=isset($title)?" title='$title'":''?>>
		<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>	
	</div>
	<input id="<?=$id2?>" name="<?=$name?>" value="<?=$sql_date?>" type="hidden">
</div>
<script>	
	$('#<?=$id1?>')
	.datetimepicker({locale:'<?=$locale?>', viewMode: 'days', format:"L"})
	.on('dp.change', function(){
		$('#<?=$id2?>').val($('#<?=$id1?>').data('DateTimePicker').date().format('YYYY-MM-DD'));
	});
</script>
