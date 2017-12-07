<input type="file" name="<?=$name?>" class="collapse"  style="display: none" onchange="readURL(this)" accept="<?=Config::get("allowed-upload-types")[IMAGE_UPLOAD]?>"<?=isset($required)?" required":""?>>
<button class="<?=$class?>" onclick="$(this).prev().click()" type="button"><?=__("load-image")?></button>
