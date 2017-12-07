<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<div class="container">

	<div class="row">
		<div class="col-xs-12">
			<h1>Контакты</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<p class="text-strong brand-color"><?=__("address")?></p>
			<p class="text-light ymaps-geolink"><?=Config::get("CompanyAddress")[App::getRouter()->getLanguage()]?></p>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4">
			<p class="text-strong brand-color"><?=__("phone")?></p>
			<p class="text-light"><?=Config::get("CompanyPhone")?></p>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4">
			<p class="text-strong brand-color"><?=__("email")?></p>
			<p class="text-light"><a href="mailto:<?=Config::get("CompanyEmail")?>"><?=Config::get("CompanyEmail")?></a></p>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<p class="h1">Как нас найти</p>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div id="map" style="width: 100%; height:400px;"></div>
		</div>
	</div>
</div>


<script>
    ymaps.ready(init);
    var myMap, myPlacemark;

    function init(){     
        myMap = new ymaps.Map("map", {
            center: [60.098679, 29.955881],
            zoom: 16
        });
        myPlacemark = new ymaps.Placemark([60.098679, 29.955881], {
            hintContent: '<?=Config::get("CompanyName")[App::getRouter()->getLanguage()]?>',
            balloonContent: '<?=Config::get("CompanyAddress")[App::getRouter()->getLanguage()]?>'
        });
        
        myMap.geoObjects.add(myPlacemark);
    }
</script>