<div class="page-header">
	<h1><?=__("sign up form title")?></h1>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<div>
			<form name="login" method="post">

				<div class="form-group">
					<label for "login">
						<?=__("name")?>
					</label>
					<input type="text" name="name" placeholder="<?=__("your name")?>" class="form-control" required>
				</div>

				<div class="form-group">
					<label for "login">
						<?=__("login")?>
					</label>
					<input type="text" name="login" placeholder="<?=__("your login name")?>" class="form-control" required>
				</div>

				<div class="form-group">
					<label for "password">
						<?=__("password")?>
					</label>
					<input type="password" name="password" placeholder="<?=__("your password")?>" class="form-control" required>
				</div>

				<div class="form-group">
					<label for "confirmpassword">
						<?=__("confirm password")?>
					</label>
					<input type="password" name="confirmpassword" placeholder="<?=__("confirm your password")?>" class="form-control" required>
				</div>
				
				<div class="radio">
					<label>
						<input type="radio" name="gender" value="1">
						<?=__("male")?>
					</label>
					
					<label>
						<input type="radio" name="gender" value="0">
						<?=__("female")?>
					</label>
				</div>
				
				<div class="form-group">
					<label for "birthday">
						<?=__("birthday")?>
					</label>

					<div class="input-group date" id="dateset">
						<input type="text" class="form-control">
						<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
					</div>
					<div>
						<input type="hidden" name="birthday">
					</div>
<script>
	$(function(){
			$('#dateset')
			.datetimepicker({locale:'<?=App::getRouter()->getLanguage()?>', viewMode: 'years', format:"L"})
			.on('dp.change', function(){
				$('input[name=birthday]').val($('#dateset').data('DateTimePicker').date().format('YYYY-MM-DD'));
			});
	});
</script>
				</div>


				<div class="form-group">
					<br>
					<button type="submit" class="btn btn-lg btn-primary"><?=__("submit")?></button>
					<button type="reset" class="btn btn-lg btn-danger"><?=__("reset")?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	
</script>

