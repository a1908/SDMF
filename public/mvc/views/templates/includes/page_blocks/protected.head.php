<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="<?=CSS_PATH?>bootstrap.min.css">
<link rel="stylesheet" href="<?=CSS_PATH?>bootstrap-theme.min.css">
<link rel="stylesheet" href="<?=CSS_PATH?>bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="<?=CSS_PATH?>style.css">
<title><?=Page::getTitle()?></title>
<?php if($c = Page::getDescription()):?>
<meta name="description" content="<?=$c?>">
<?php endif; ?>
<?php if($c = Page::getKeywords()):?>
<meta name="keywords" content="<?=$c?>">
<?php endif; ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
