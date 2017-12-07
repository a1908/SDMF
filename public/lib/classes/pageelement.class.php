<?php
	
	define("MAX_PAGE_LABELS",7);

	class PageElement{
		
		// creates list of options for select element
		// input parameter $options - array of objects with id and value properties
		public static function listOptions($options,$selected=null){
			if(is_array($selected)){
				$selected_options = &$selected;
			}else{
				$selected_options[] = $selected;
			}
			ob_start();
			foreach($options as $option){
?>
<option value="<?=$option->id?>"<?=in_array($option->id,$selected_options,true)?" selected":""?>><?=$option->value?></option>
<?php				
			}
			return ob_get_clean();
		}
		
//	Drop Down element 
//		

		public static function dropDown($name, $attr, $options, $default = null, $selected = null){

			ob_start();
?>
	<select name="<?=$name?>"<?=$attr?" ".$attr:""?>>
<?php
			if( $default ){		
?>			
		<option value="" disabled<?=$selected?"":" selected"?>><?=$default?></option>
<?php
			}

			foreach($options as $k=>$o){
?>
		<option value="<?=$k?>"<?=($selected==$k)?" selected":""?>><?=$o?></option>
<?php
			}
?>
	</select>
<?php
			return ob_get_clean();						
		}
		
// -------------------------------------
// Display value
// $title - field title
// $output - value to display
// $class - class to add
		public static function displayValue($title, $output, $class = null, $editable=null, $callback=null){
			ob_start();
?>
<p>
	<span class="text-small text-muted"><?=$title?></span>
<?php 
			if($editable){
?>
	&nbsp;<span class="glyphicon glyphicon-edit action-icon action-icon-primary" onclick="<?=$callback?>"></span>
<?php
			}		
?>
	<br>
	<span class="text-big <?=$class?>"><?=$output?></span>
</p>
<?php
			return ob_get_clean();						
		}		
	
	
// --------------------------------------
// Pagination
// displays first, prev,  page numbers, next, last

		public static function pagination($total_pages, $active_page, $page_link, $onclick=null){
			if( $total_pages == 1 ){
				return "";
			}
			$page_link = App::getWebRoot().$page_link;
			
			if( $total_pages <= MAX_PAGE_LABELS){
				
				$page_labels = range(1,$total_pages);
				
			}elseif( $active_page <= MAX_PAGE_LABELS - 1 ){
				
				$page_labels = range(1, MAX_PAGE_LABELS);
				
			}elseif( ($active_page > MAX_PAGE_LABELS - 1) && ($active_page - 1 <= $total_pages - MAX_PAGE_LABELS) ){
				
				$page_labels = range($active_page-1, $active_page-1+MAX_PAGE_LABELS-1);
				
			}elseif( $active_page - 1 > $total_pages - MAX_PAGE_LABELS){
				
				$page_labels = range($total_pages - MAX_PAGE_LABELS+1, $total_pages);
				
			}
			
			$prev = $active_page-1;			
			$next = $active_page+1;
			
			ob_start();
?>
		<nav aria-label="Page navigation">
		  <ul class="pagination">
<?php
			if( $active_page == 1 ){
?>
		    <li class='disabled'>
		      <span title="<?=__("button-previous")?>">
		        <span aria-hidden="true">&laquo;</span>
		      </span>
		    </li>
<?php
			}else{
?>
		    <li>
		      <a href="<?=$page_link."/".$prev?>" aria-label="Previous" title="<?=__("button-previous")?>" page="<?=$prev?>"<?=$onclick?" onclick='$onclick'":""?> >
		        <span aria-hidden="true">&laquo;</span>
		      </a>
		    </li>
<?php
			}
?>
<?php
				foreach($page_labels as $p){
?>
		    <li<?=$p==$active_page?" class='active'":""?>><a href="<?=$page_link."/".$p?>" title="<?=__("Page-number").$p?>"<?=$onclick?" onclick='$onclick'":""?> page="<?=$p?>"><?=$p?></a></li>
<?php
				}
?>
<?php
			if( $active_page==$total_pages ){
?>
		    <li class='disabled'>
		      <span title="<?=__("button-next")?>">
		        <span aria-hidden="true">&raquo;</span>
		      </span>
		    </li>
<?php
			}else{
?>
		    <li>
		      <a href="<?=$page_link."/".$next?>" aria-label="Next" title="<?=__("button-next")?>" page="<?=$next?>"<?=$onclick?" onclick='$onclick'":""?>>
		        <span aria-hidden="true">&raquo;</span>
		      </a>
		    </li>
<?php
			}
?>
		  </ul>
		</nav>		

<?php			
			return ob_get_clean();
		} 

// --------------------------------------
// number of items on the page selection
// 
		public static function selectItemsOnPage($current_value=null){
			$options = 	Config::get("items_on_page");
			ob_start();
	?>
		<form name="items_on_page" method="post">
			<input type="hidden" name="action" value="items_on_page">
			<label><?=__("Label-Items-on-page")?></label>
			<select class="form-control" name="items_on_page" onchange="$(this).parent().submit()">
	<?php
			foreach( $options as $k=>$o ){
	?>
				<option value="<?=$k?>"<?=$k==$current_value?" selected":""?>><?=__($o)?></option>
	<?php
			}
	?>
			</select>
		</form>
	<?php		
			return ob_get_clean();
		}

// --------------------------------------
// Active table
// output table with ability to edit row by clicking on them and delete rows
// 

		public static function activeTable($rowid, $captions, $data, $update_row, $delete_row, $add_row){
			
		}
		
		public static function outputData($data, $type=null, $class=null, $style=null, $tag="span"){
			$class = $class?" class='$class'":"";
			$style = $style?" style='$style'":"";
			
			$output = "";
			switch($type){
								
				case "url":
					$href = htmlspecialchars($data);
					$output = "<a href='$href' target='blank'$class$style>$href</a>";
				break;
				
				case "email":
					$mail = htmlspecialchars($data);
					$output = "<a href='mailto:$mail'$class$style>$mail</a>";
				break;
				
				case "date":
					$output = "<$tag$class$style>".date(Config::get("display_date_format")[App::getRouter()->getLanguage()], strtotime($data))."</$tag>";
				break;

				case "image":
					if(is_file(ROOT.DS."www".DS.$data)){
						$data = WEB_ROOT.$data;
						$output =  "<img$class$style src='$data'>";
					}else{
						$output = "<span>".NO_VALUE_SIGN."</span>";
					}
				break;
				
				case "textarea":
					
					$tag = "p";
								
				case "foreign_key":
				case "multiple_foreign_keys":
				case "external_link":
				default:
					$output = "<$tag$class$style>".nl2br(htmlspecialchars($data))."</$tag>";
				break;
				
			}
			return $output;
		}
	
		public static function datePicker($name, $attr=null, $date=null){

			$language = App::getRouter()->getLanguage();
			$locale = Config::get("datepicker_locale")[$language];
			$date_format = Config::get("display_date_format")[$language];
			if( $date ){
				$display_date = date( $date_format, strtotime($date));
				$sql_date = $date;
			}else{
				$display_date = "";
				$sql_date = "";
			}
			
			$id1 = uniqid("datepicker-");
			$id2 = uniqid("datepicker-");
			ob_start();
?>
<div>
	<div class="input-group date"  id="<?=$id1?>">
		<input type="text" value="<?=$display_date?>"<?=isset($attr)?" $attr":""?>>
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

<?php
			return ob_get_clean();
		}
	
	
	
	}
	