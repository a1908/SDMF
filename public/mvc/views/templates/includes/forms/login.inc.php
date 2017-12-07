<?php
	if(isset($_GET["next_page"])){		
		$next_page = $_GET["next_page"];
	}else{
		$next_page = "";
	} 
?>
<div class="page-header">
	<h1><?=__("login form title")?></h1>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<div>
			<form name="login" method="post" onsubmit="processForm(this); return false;">
				<div class="form-group">
					<label for "login" class="control-label">
						<?=__("login")?>
					</label>
					<input type="text" name="<?=LOGIN_FIELD?>" placeholder="<?=__("your login name")?>" class="form-control" required>
				</div>
				<div class="form-group">
					<label for "password" class="control-label">
						<?=__("password")?>
					</label>
					<input type="password" name="<?=PASSWORD_FIELD?>" placeholder="<?=__("your password")?>" class="form-control">
				</div>
				<!--div class="checkbox">
					<label for "rememberme">
						<input type="checkbox" name="rememberme"> <?=__("remember me")?>
					</label>
				</div-->
				<div class="form-group">
					<button type="submit" class="btn btn-lg btn-primary"><?=__("submit")?></button>
					<button type="reset" class="btn btn-lg btn-danger"><?=__("reset")?></button>
				</div>
				<div>
					<input type="hidden" value="<?=$next_page?>" name="next_page">
				</div>
			</form>
		</div>
		<div>
			<!--p><a href="<?=WEB_ROOT?>/passwordrestore"><?=__("forgot your password?")?></a></p>
			<p><a href="<?=WEB_ROOT?>/signin"><?=__("don't have an account? Sign in")?></a></p-->
		</div>
	</div>
</div>

<script src="<?=WEB_ROOT?>/js/processForm.js"></script>

