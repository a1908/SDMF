<div style="margin:0 auto; width:75%; text-align: center">
<h1><?=__("error-page-not-found-message")?></h1>
<p>
<?=WEB_ROOT."/".htmlspecialchars(str_replace("404/","",App::getRouter()->getUrl()))?>
</p>
		
<p> <a href="<?=WEB_ROOT.Zone::getWebPrefix().App::getRouter()->getLanguagePath("/%")?>"><?=__("error-page-not-found-offer")?></a></p>

</div>