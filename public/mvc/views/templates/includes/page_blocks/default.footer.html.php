	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-4">
				<p class="text-strong brand-color">
					<?=Config::get("CompanyName")[App::getRouter()->getLanguage()]?>
				</p>
				<address>
					<p class="text-light">			
						<?=Config::get("CompanyAddress")[App::getRouter()->getLanguage()]?>
					</p>
				</address>
			</div>
			
			<div class="col-xs-12 col-sm-4">
				<p class="text-strong brand-color"><?=__("contacts")?></p>
				<p class="text-light">
					<?=Config::get("CompanyPhone")?>
					<br>
					<a href="mailto:<?=Config::get("CompanyEmail")?>"><?=Config::get("CompanyEmail")?></a>
				</p>
			</div>
			
		</div>
	</div>
	
