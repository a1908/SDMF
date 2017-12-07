<?php
	function level_up($x){
		return $x->value;
	}					
	
	class Model{
		
		protected $model_name;
		protected $display_value;
		protected $fields_list;
		protected $fields_caption;
		protected $fields_type;
		protected $fields_number_type;
		protected $fields_required;
		protected $fields_validate;
		protected $fields_default;
		protected $foreign_keys_data_source;
		protected $multiple_foreign_keys;
		protected $external_links;
		protected $table_ref;
		protected $SQL_fields_list;
		protected $data;
		protected $data_fk;
		protected $data_mfk;
		protected $data_ext;
		protected $recordset;
		protected $recordset_fk;
		protected $recordset_mfk;
		protected $recordset_ext;		
		
		// output parameters 
		protected $select_size = 5;
		protected $textarea_rows = 5;
		protected $mfk_output_separator = "\n";
		protected $horizontal_form_screen_breakpoint = "sm";
		protected $horizontal_form_cols_breakpoint = 2;
				
		// $id - model id for DATA_DEFS_XML.xml file
		// $fields - comma separated list of fields (string)
		public function __construct($id=null, $fields=null){
			if(!$id){
				return;
			}
			$this->model_name = $id;
			
			if( $fields ){
				// get fields list if any
				foreach(explode(",",$fields) as $k=>$f){
					$this->fields_list[$k] = trim($f);
				}				
			}

			$lang = App::getRouter()->getLanguage();
			$xmldefs = App::getDataXmlDefs();

			$model = $xmldefs->xpath("//model[@id='$id']");
			if($model){
				$this->table_ref = $model[0]->table;
				$this->display_value = $model[0]->display_value;
				
				if( $fields ){
					// if fields list is defined 					
					$fields_collection = array();
					foreach($this->fields_list as $k=>$f){				
						$fields_collection[$k]  = "//model[@id='$id']/field[@id='$f']";
					}
					
					$fields_defs =$xmldefs->xpath(implode("|", $fields_collection));

				}else{
					//default fields list 
					$fields_defs = $xmldefs->xpath("//model[@id='$id']/field");
				}

				// get fields definitions data
				foreach($fields_defs as $fd){
					$fn = (string)$fd['id'];
					$type = (string)$fd['type'];
					
					if(!$fields){
						$this->fields_list[] = $fn;						
					}
										
					$this->fields_type[$fn] = $type;
					if( $type=="number" ){
						$this->fields_number_type[$fn] = isset($fd['step'])?(string)$fd['step']:"any";
					}
					$this->fields_caption[$fn] = $fd->caption->{$lang};

					if( isset($fd['default']) ){
						$this->fields_default[$fn] = (string)$fd['default'];
					}
					
					if( isset($fd['required']) ){
						$this->fields_required[] = $fn;
					}

					if( isset($fd['validate']) ){
						$this->fields_validate[] = $fn;
					}					

					if( $type == "foreign_key" ){
						$this->foreign_keys_data_source[$fn] = $fd->data_source;
					}

					switch( $type ){
						case "external_link":
							$external_fields = null;
							if( isset($fd->fields) ){
								$external_fields = (string)$fd->fields;
							}
							$this->external_links[$fn]["model"] = new Model($fd->model, $external_fields);
							$this->external_links[$fn]["link"] = $fd->link;
						break;
						
						case "multiple_foreign_keys":
							$this->multiple_foreign_keys[$fn]["link_table"] = $fd->link_table;
							$this->multiple_foreign_keys[$fn]["internal_key"] = $fd->internal_key;
							$this->multiple_foreign_keys[$fn]["external_key"] = $fd->external_key;
							$this->multiple_foreign_keys[$fn]["data_source"] = $fd->data_source;
							$this->multiple_foreign_keys[$fn]["object"] = new MultipleForeignKeys($fd->link_table,$fd->internal_key,$fd->external_key,$fd->data_source);
						break;
						
						case "date":
							// format date  for language settings
							$expr = isset($fd->expr)?$fd->expr:$fn;
							
							$SQL_fields_list[] = $expr." as ".$fn;	
						
						break;
						
						default:
							// external link and multiple foreign keys fields are not contained in the table
							if( isset($fd->expr)){
								$SQL_fields_list[] = $fd->expr." as ".$fn;	
							}else{
								$SQL_fields_list[] = $fn;
							}
						break;
					}
				}
				$this->SQL_fields_list = implode(",", $SQL_fields_list);
			}else{
				$this->table_ref = $id;
				$this->SQL_fields_list = "*";
			}
		}
		
		public function getModelName(){
			return $this->model_name;
		}

		public function validate($params){
			foreach($params as $fld=>$value){
				switch($this->fields_type[$fld]){
					case "number":
						if( !is_numeric($value) ){
							$params[$fld] = 0;
						}
					break;
					
					case "url":
					break;
				}
			}
			return $params;			
		}

		// $values - associative array of values "name"=>'name_value'				
		public function createRecord($values){
			
			foreach($values as $k=>$v){
				$param[":".$k] = $v;
				$set[] = "$k=:$k";
			}
			
			$sql = "insert into ".$this->table_ref." set ".implode(",",$set);
			
			$stmt = App::$db->prepare($sql);
			$stmt->execute($param);
			
			return App::$db->lastInsertId();
		}

		// $values - associative array of values "name"=>'name_value'				
		// $cond - associative array of condition values for where statement				
		public function updateRecord($cond,$values){
			
			foreach($cond as $k=>$v){
				$param[":".$k] = $v;
				$where[] = "$k=:$k";
			}
			
			
			foreach($values as $k=>$v){
				$param[":".$k] = $v;
				$set[] = "$k=:$k";
			}
			
			$sql = "update ".$this->table_ref." set ".implode(",",$set)." where ".implode(" and ",$where);
			$stmt = App::$db->prepare($sql);
			$stmt->execute($param);
		}
		
		// $cond - associative array of condition values for where statement				
		public function deleteRecord($cond){
			foreach($cond as $k=>$v){
				$param[":".$k] = $v;
				$where[] = "$k=:$k";
			}
			$sql = "delete from ".$this->table_ref." where ".implode(" and ",$where);
			$stmt = App::$db->prepare($sql);
			$stmt->execute($param);
		}

		public function getRecord($sql_addition=null, $param=null){
			$stmt = App::$db->prepare("select ".$this->SQL_fields_list." from ".$this->table_ref." ".$sql_addition);
			$stmt->execute($param);

			if($stmt->rowCount()==0)
				return false;
			else{
				$data = $stmt->fetch();
				foreach(get_object_vars($data) as $k=>$v){
					$this->data[$k] = $v;
				}				
			}

			// get data for foreign keys
			if($this->foreign_keys_data_source){
				foreach($this->foreign_keys_data_source as $k=>$v){
					if( $this->data[$k] ){
						$ds = new DataSource($v,$this->data[$k]);
						$this->data_fk[$k] = $ds->getValue();
					}
				}
			}
			
			// get data for multiple foreign keys
			if( $this->multiple_foreign_keys ){
				foreach( $this->multiple_foreign_keys as $k=>$v ){
					$keys_array = $v["object"]->getExternalKeys($this->data['id']);
					if($keys_array){
						// getKeyValuePairs - returns array Key=>Value
						$this->data_mfk[$k] = $v["object"]->getKeyValuePairs(implode(",",$keys_array));	
					}
				}
			}

			// get data for external links
			if( $this->external_links ){
				foreach( $this->external_links as $k=>$v ){					
					$this->data_ext[$k] = $v["model"]->getRecordSet("where `{$v["link"]}`='{$this->data['id']}'");
				}
			}
			
			return true;
		}
		
		public function getRecordSet($sql_addition=null, $param=null, $order_by = null, $limit = null, $offset = null){
			$sql = "select ".$this->SQL_fields_list." from ".$this->table_ref." ".$sql_addition;
			if( $order_by ){
				$sql .= " order by $order_by";
			}
			
			if( $limit ){
				if( $order_by ){
					$sql .= " limit $limit";
				}else{
					$sql .= " order by id limit $limit";
				}
				
				// without limit offset does not make sense
				if( $offset){
					$sql .= " offset $offset";
				}
			}
			
			$stmt = App::$db->prepare($sql);
			$stmt->execute($param);
			
			$this->recordset = $stmt->fetchAll();

			if( $this->foreign_keys_data_source || $this->multiple_foreign_keys ){		
				foreach($this->recordset as $k=>$rec){
					
					if($this->foreign_keys_data_source){
						foreach( $this->foreign_keys_data_source as $fld=>$dc_id){
							if( $rec->$fld ){
								$dc = new DataSource($dc_id,$rec->$fld);
								$this->recordset_fk[$k][$fld] = $dc->getValue();
							}
						}
					}
					
					if( $this->multiple_foreign_keys ){
						foreach( $this->multiple_foreign_keys as $fld=>$mfk){
							$keys_array = $mfk["object"]->getExternalKeys($rec->id);
							if($keys_array){
								$this->recordset_mfk[$k][$fld] = $mfk["object"]->getKeyValuePairs(implode(",",$keys_array));	
							}
						}
					}

					// get data for external links
					if( $this->external_links ){
						foreach( $this->external_links as $fld=>$ext ){					
							$this->recordset_ext[$k][$fld] = $ext["model"]->getRecordSet("where `{$ext["link"]}`='{$rec->id}'");
						}
					}
			
				}
			}
			return $this->recordset;
		}
		
		
		// filter - listViewStatus class
		public function getFilteredRecords($filter, $order=null, $page=1, $condition=null){
			
			if( $condition ){
				$where[] = "($condition)";
			}else{
				$where = null;
			}

			$params = null;
			if( empty($order) ){
				$order = "id";
			}
			$limit = $filter->getItemsOnPage();
			$offset = $limit*($page-1);
			
			
			
			if( $text_filter = $filter->getTextFilter() ){
				$f = $this->filterAllTextFields($text_filter);
				$where[] = "(".$f["sql"].")";
				$params = $f["params"]; 
			}
		
			$fields_filter = $filter->getFieldsFilter();
			foreach( $fields_filter as $k=>$v){
				if( !$v ){
					continue;
				}
				$filter = $this->filterField($k,$v);
				$where[] = "(".$filter["sql"].")";
			}
			
			$where = $where?"where ".implode(" and ", $where):null;
			$this->getRecordSet( $where, $params, $order, $limit, $offset);
			return  ($this->getRecordCount($where, $params));
		}
			
		
		// create sql statement and parameters to filter all text fiels
		public function filterAllTextFields($text){
			$sql = null;
			$params = null;
			foreach($this->fields_type as $fld=>$type){
				switch($type){
					case "text":
					case "textarea":
						$sql[] = $fld." like :".$fld."";
						$params[":".$fld] = "%$text%";
					break;	
				}
			}
			
			$filter["sql"] = $sql?implode(" or ",$sql):null; 
			$filter["params"] = $params;
			return $filter;			
		}
		
		
		// create sql statement and parameters to filter the $fld field that contains $values - condition logical OR 
		public function filterField($fld,$values){
			$sql = null;
			$params = null;
			$filter["sql"] = null; 
			$filter["params"] = null;
			switch( $this->fields_type[$fld] ){
				case "text":
				case "textarea":
					if( is_array($values) ){
						$cnt = 0;
						foreach($values as $v){
							$sql[] = $fld." like :".$fld.$cnt;
							$params[":".$fld.$cnt] =  "%$v%";
							$cnt++;
						}
						if( $cnt ){
							$filter["sql"] = $sql?implode(" or ",$sql):null; 
							$filter["params"] = $params;
						}
					}else{
						$filter["sql"] = $fld." like :".$fld;
						$filter["params"][":".$fld] = "%$values%";
					}
				break;
				
				case "foreign_key":
					if( $values ){
						if( is_array($values) ){
							foreach($values as $k=>$v){
								$values[$k] = "'".$v."'";
							}
							$filter["sql"] = $fld." in (".implode(",",$values).")";
						}else{
							$filter["sql"] = $fld." = '$values'";
						}
					}
				break;
				
				case "multiple_foreign_keys":
					if( $values ){
						$link_table = $this->multiple_foreign_keys[$fld]["link_table"];
						$internal_key = $this->multiple_foreign_keys[$fld]["internal_key"];
						$external_key = $this->multiple_foreign_keys[$fld]["external_key"];
						if( is_array($values) ){
							foreach($values as $k=>$v){
								$values[$k] = "'".$v."'";
							}
							$filter["sql"] = "id in (select $internal_key from $link_table where $external_key in (".implode(",",$values)."))";
						}else{
							$filter["sql"] = "id in (select $internal_key from $link_table where $external_key  = '$values')";
						}						
					}
				break;
				
				default:					
				break;
			}
			return $filter;
		}
		
		// remove a file $name
		// if $name is a directory deletes all content recursively
		public function removeFile($name){
			if( is_dir($name) ){
				// empty the folder
				$files = scandir($name);
				foreach($files as $f){
					if( $f == "." || $f == ".." ){
						continue;
					}
					$this->removeFile($name.DS.$f);
				}
				rmdir($name);
			}else{
				unlink($name);
			}
		}

		// copies a file $name to the $dest folder 
		// if $name is a directory copies all files except directories into the dest folder
		public function copyFile($name,$dest){
			$basename = basename($name);
			if( is_dir($dest) && !is_dir($name) ){
				copy($name,$dest.DS.$basename);
			}elseif( !is_dir($dest) && !is_dir($name) ){
				copy($name,$dest);
			}elseif( is_dir($name) ){
				// copy the folder
				$files = scandir($name);

				foreach($files as $f){
					if( $f == "." || $f == ".." ){
						continue;
					}
					
					// if file $f is a folder create destination and copy
					if( is_dir($name.DS.$f) ){
						mkdir($dest.DS.$f);
					}
					$this->copyFile($name.DS.$f,$dest.DS.$f);
				}
			}
		}
		
		public function recordsetCount(){
			return count($this->recordset);
		}
		
		public function recordsetData(){
			return $this->recordset;
		}
		
		public function getData($fld=null){
			if($fld){
				return $this->data[$fld];
			}else{
				return $this->data;
			}
		}
				
		protected function setFieldsCaption($captions){
			$this->fields_caption = $captions;
		}
		
		protected function setFieldsType($type){
			$this->fields_type = $type;
		}

		public function getExternalLinkModel($fld){
			return $this->external_links[$fld]["model"];
		}

		public function setSelectSize($size){
			$this->select_size = $size;
		}

		public function setTextareaRows($rows){
			$this->textarea_rows = $rows;
		}

		public function setMfkOutputSeparator($separator){
			$this->mfk_output_separator = $separator;
		}

		public function setHorizontalFormScreenBreakpoint($sc){
			if( in_array($sc,array("xs","sm","md","lg")) ){
				$this->horizontal_form_screen_breakpoint = $sc;
			}
		}

		public function setHorizontalFormColsBreakpoint($c){
			if( $c>=1 && $c<=11 ){
				$this->horizontal_form_cols_breakpoint = $c;
			}
		}

		public function getHorizontalFormColsBreakpoint(){
			return $this->horizontal_form_cols_breakpoint;
		}

		public function getHorizonalFormOffsetClass(){
			$sc = $this->horizontal_form_screen_breakpoint;
			$off = $this->horizontal_form_cols_breakpoint;
			$length = 12 - $off;
			return "col-$sc-offset-$off col-$sc-$length col-xs-12";
		}

		public function getRawData($fld, $index=null){

			if( $index === null ){
				$data = &$this->data[$fld];
				$data_fk = &$this->data_fk[$fld];
				$data_mfk = &$this->data_mfk[$fld];
				$data_ext = &$this->data_ext[$fld];
			}else{
				$data = &$this->recordset[$index]->$fld;
				$data_fk = &$this->recordset_fk[$index][$fld];
				$data_mfk = &$this->recordset_mfk[$index][$fld];
				$data_ext = &$this->recordset_ext[$index][$fld];
			}
			
			switch($this->fields_type[$fld]){
								
				default:
				case "url":
				case "email":
				case "date":
				case "image":
				
				break;
								
				case "foreign_key":
					$data = $data_fk;
				break;
				
				case "multiple_foreign_keys":
					$data = implode($this->mfk_output_separator,$data_mfk);
				break;

				case "external_link":
					$data = count($data_ext);
				break;
			}
			return $data;
		}

		public function getOutputData($fld, $index=null, $class=null, $style=null){
			
			return PageElement::outputData($this->getRawData($fld, $index), $this->fields_type[$fld], $class, $style);			
		}

		public function getFieldValue($fld, $index = null){

			if( $index === null ){
				$data = &$this->data[$fld];
				$data_fk = &$this->data_fk[$fld];
				$data_mfk = &$this->data_mfk[$fld];
				$data_ext = &$this->data_ext[$fld];
			}else{
				$data = &$this->recordset[$index]->$fld;
				$data_fk = &$this->recordset_fk[$index][$fld];
				$data_mfk = &$this->recordset_mfk[$index][$fld];
				$data_ext = &$this->recordset_ext[$index][$fld];
			}
	
			$field_value = "";
			switch($this->fields_type[$fld]){
								
				case "url":
					$href = htmlspecialchars($data);
					return "<a href='$href' target='blank'>$href</a>";
				break;
				
				case "email":
					$mail = htmlspecialchars($data);
					return "<a href='mailto:$mail'>$mail</a>";
				break;
				
				case "date":
					if( $data ){
						return date(Config::get("display_date_format")[App::getRouter()->getLanguage()], strtotime($data));
					}else{
						return "-";
					}
				break;

				case "image":
					if( is_file(WEB_ROOT_LOCAL.$data) ){
						return "<img src='".WEB_ROOT."$data'>";
					}else{
						return "-";
					}
					
				break;
								
				case "foreign_key":
					$field_value = $data_fk;
				break;
				
				case "multiple_foreign_keys":
					if( $data_mfk ){
						$field_value = implode($this->mfk_output_separator,$data_mfk);
					}
				break;

				case "external_link":
					$field_value = count($data_ext);
				break;

				
				default:
					 $field_value = $data;				
			}
			return nl2br(htmlspecialchars($field_value));
		}
		
		public function getRecordCount($sql_addition=null, $param=null){			
			$stmt = App::$db->prepare("select count(*) from {$this->table_ref} $sql_addition");
			$stmt->execute($param);
			return $stmt->fetchColumn();
		}
		
		public function getFieldsList($exclude=null){
			if( !$exclude ){
				return $this->fields_list;
			}else{
				$excluded_fields = array_map("trim", explode(",", $exclude));
				return array_diff($this->fields_list,$excluded_fields);						
			}
		}
		
		public function getFieldsType(){
			return $this->fields_type;
		}
		
		public function getType($fld){
			return $this->fields_type[$fld];
		}
		
		public function getFieldsCaption(){
			return $this->fields_caption;
		}

		public function getCaption($fld){
			return $this->fields_caption[$fld];
		}

		public function getFieldsRequired(){
			return $this->fields_required;
		}

		public function getFieldsValidate(){
			return $this->fields_validate;
		}

		public function getForeignKeysDataSource(){
			return $this->foreign_keys_data_source;
		}

		public function horizonalFormInputHTML($fld,$update=null){
			$sc = $this->horizontal_form_screen_breakpoint;
			$off = $this->horizontal_form_cols_breakpoint;
			$length = 12 - $off;

?>
			<div class="form-group">
				<label for "<?=$fld?>" class="control-label col-<?=$sc?>-<?=$off?> col-xs-12">
					<?=$this->fields_caption[$fld]?>
				</label>
				<div class="input-group col-<?=$sc?>-<?=$length?> col-xs-12">
					<?php $this->fieldInputHTML($fld,$update); ?>
				</div>
			</div>
<?php			
		}

		public function basicFormInputHTML($fld,$update=null){
?>
			<div class="form-group">
				<label for "title" class="control-label">
					<?=$this->fields_caption[$fld]?>
				</label>
				<?=$this->fieldInputHTML($fld,$update);?>
			</div>
<?php			
		}

		public function fieldInputHTML($fld, $update=false, $index = null, $class=null){
			
			if( $index === null ){
				$data = &$this->data[$fld];
				$data_fk = &$this->data_fk[$fld];
				$data_mfk = &$this->data_mfk[$fld];
				$data_ext = &$this->data_ext[$fld];
			}else{
				$data = &$this->recordset[$index]->$fld;
				$data_fk = &$this->recordset_fk[$index][$fld];
				$data_mfk = &$this->recordset_mfk[$index][$fld];
				$data_ext = &$this->recordset_ext[$index][$fld];
			}

			switch($this->fields_type[$fld]){
				default:
				case "text":
				case "email":
				case "tel":
				case "url":
				case "number":
					$type = $this->fields_type[$fld];
					$name = $fld;
					$class = $class?$class:"form-control";
								
					if( $update ){
						$value = $data;
					}else{
						if( isset($this->fields_default[$fld]) ){
							$value = $this->fields_default[$fld];
						}
					}
					
					if( $this->fields_required ){
						if( in_array($fld, $this->fields_required) ){
							$required = true;
						}
					}
					
					if( $this->fields_validate ){
						if( in_array($fld, $this->fields_validate) ){
							$validate = true;
						}	
					}
					
					if( isset($this->fields_number_type[$fld]) ){
						$step = $this->fields_number_type[$fld];
					}

					require SNIPPETS_PATH."field-input.html.php";
					
				break;
				
				case "textarea":
					$name = $fld;
					$class = $class?$class:"form-control";
					if( $update ){
						$value = $data;
					}
					
					$rows = $this->textarea_rows;
					if( $this->fields_required ){
						if( in_array($fld, $this->fields_required) ){
							$required = true;
						}
					}					
					require SNIPPETS_PATH."field-textarea.html.php";
				break;
				
				case "date":
					$name = $fld;
					$language = App::getRouter()->getLanguage();
					$locale = Config::get("datepicker_locale")[$language];
					$date_format = Config::get("display_date_format")[$language];
					if( $update ){
						$display_date = date( $date_format, strtotime($data));
						$sql_date = $data;
					}else{
						$display_date = date($date_format);
						$sql_date = date("Y-m-d");
					}
					
					$id1 = uniqid("datepicker-");
					$id2 = uniqid("datepicker-");
										
					require SNIPPETS_PATH."field-datepicker.html.php";
				break;
				
				case "foreign_key":
					$class = $class?$class:"form-control";
					$dc = new DataSource($this->foreign_keys_data_source[$fld]);
					$attr = "class='$class'".(in_array($fld, $this->fields_required)?" required":"");
					$dc->selectHTML($fld, $update?$data:null, $attr);
				break;

				case "multiple_foreign_keys":
					$class = $class?$class:"form-control";
					$dc = new DataSource($this->multiple_foreign_keys[$fld]["data_source"]);
					$attr = "class='$class'".(in_array($fld, $this->fields_required)?" required":"");
					$dc->multipleHTML($fld, $update&&$data_mfk?array_keys($data_mfk):null, $attr, $this->select_size);
				break;
				
				case "external_link":
				break;
				
				case "image":
					$name = $fld;
					if( $this->fields_required ){
						if( in_array($fld, $this->fields_required) ){
							$required = true;
						}
					}
					require SNIPPETS_PATH."field-image-upload.html.php";
				break;
			}
		}
		
		// output table header row excluding fields listed in $exclude - comma-separated list
		public function tableHeaderRowHTML($exclude=null,$before=null,$after=null){
			
			if( $exclude ){
				$excluded_fields = array_map("trim", explode(",", $exclude));
			}else{
				$excluded_fields = array();
			}
			echo "<tr>";
			if($before){
				echo "<th>$before</th>";
			}
			foreach($this->fields_list as $v){ 
				
				if( in_array($v, $excluded_fields) ) continue;
				
				echo "<th>".$this->fields_caption[$v]."</th>";
			}
			if($after){
				echo "<th>$after</th>";
			}
			echo "</tr>";
		}
		
		//output table rows with row marks array marking the row
		public function tableRowsHTML($row_marks=null, $exclude=null){
						
			if( $exclude ){
				$excluded_fields = array_map("trim", explode(",", $exclude));
			}else{
				$excluded_fields = array();
			}
						
			foreach( $this->recordset as $recid=>$rec){
				$row_mark = "";
				if( $row_marks ){
					foreach($row_marks as $k=>$v){
						$row_mark .= " $k='{$rec->$v}'";
					}
				}
				echo "<tr$row_mark>";
				foreach( $this->fields_list as $fld ){
				
					if( in_array($fld, $excluded_fields) ) continue;
					echo "<td>";
					echo $this->getFieldValue($fld, $recid);
					echo "</td>";
				}
				echo "</tr>";
			}
			
		}

		// $title - field title
		// $output - value to display
		public function customOutputHTML($title, $output, $class = null){
			require SNIPPETS_PATH."field-output.html.php";
		}
		
		// display fields in forms
		public function displayFieldValue($fld, $class=null, $editable = null, $callback=null){

			$value = $this->fields_type[$fld];
			
			switch( $this->fields_type[$fld] ){
				default:
					 $value = nl2br(htmlspecialchars($this->data[$fld]));
				break;
				
				case "url":
					$href = htmlspecialchars($this->data[$fld]);
					return "<a href='$href' target='blank'>$href</a>";
				break;
				
				case "email":
					$mail = htmlspecialchars($this->data[$fld]);
					return "<a href='mailto:$mail'>$mail</a>";
				break;
				
				case "date":
					return date(Config::get("display_date_format")[App::getRouter()->getLanguage()], strtotime($this->data[$fld]));
				break;
				
				case "image":
					return "<img src='{$this->data[$fld]}' class='$class'>";
				break;
								
				case "foreign_key":
					if( !empty($this->data_fk[$fld]) ){
						$value = $this->data_fk[$fld];
					}else{
						$value = NO_VALUE_SIGN;
					}
				break;
				
				case "multiple_foreign_keys":
					if( isset($this->data_mfk[$fld]) ){
						$value = nl2br(htmlspecialchars(implode("\n",$this->data_mfk[$fld])));
					}else{
						$value = NO_VALUE_SIGN;
					}
				break;
				
				case "external_link":
					$v = $this->external_links[$fld]["model"]->display_value;
					$t = $this->external_links[$fld]["model"]->table_ref;
					$pid = $this->external_links[$fld]["link"];
					$id = $this->data['id'];
					$sql = "select $v as value from $t where `$pid`='$id'";
					$stmt = App::$db->query($sql);
					$list = array_map("level_up",$stmt->fetchAll());
					
					if( $list ){
						$value = nl2br(htmlspecialchars(implode("\n",$list)));
					}else{
						$value = NO_VALUE_SIGN;
					}					
				break;
			}
			echo PageElement::displayValue(htmlspecialchars($this->fields_caption[$fld]), $value, $class, $editable, $callback);
		}		
		
		//display fields in tables and forms
		public function fieldOutputHTML($fld, $class = null, $context_menu = null){
			if( $this->fields_type[$fld] != "external_link" ){

				$this->customOutputHTML($this->fields_caption[$fld],  $this->getFieldValue($fld), $class);

			}else{
				$table_id = uniqid("table-");
?>
			<p>
				<span class="text-small text-muted">
					<?=$this->fields_caption[$fld]?>&nbsp;
					<a href="#" data-toggle="tooltip" data-placement="top" title="<?=__("Model-Class-message-add-new")?>" onclick="$('#<?=$table_id?> tr.<?=$fld?>-add-new').show();$('.empty-row').hide();return false;">
						<span class="glyphicon glyphicon-plus-sign action-icon action-icon-positive"></span>
					</a>
				</span>
				<br>
			
<?php
				$em = $this->external_links[$fld]["model"];
				$exclude = "id,".$this->external_links[$fld]["link"];
				$field_list = $em->getFieldsList($exclude);
				$count = count($field_list);
?>

				<table id="<?=$table_id?>"<?=$context_menu?"class='table table-hover has-context-menu' contextmenu='$context_menu'":"class='table'"?>>
					<thead>
						<?php $em->tableHeaderRowHTML($exclude); ?>
					</thead>
					<tbody>
<?php
				if( $em->recordsetCount() == 0 ){
?>
						<tr class="empty-row"><td colspan="<?=$count?>"><span class="text-big  <?=$class?>"><?=__("Model-Class-message-No-linked-records-found")?></span></td></tr>
<?php					
				}else{
					if( $context_menu ){
						foreach( $this->data_ext[$fld] as $index=>$rec){
?>
						<form method="post" name="<?=$fld.$index?>" onsubmit="processForm(this); return false;">	
							<tr rowid='<?=$index?>'>
								<?php foreach( $field_list as $f ){ ?>
								<td width="<?=100/$count?>%">
									<div class="display-value">
										<?=$em->getFieldValue($f, $index)?>
									</div>
									<div class="input-value collapse"><?=$em->fieldInputHTML($f, true, $index)?></div>
								</td>
								<?php } ?>
							</tr>
							<tr class="input-value collapse">
								<td colspan="<?=$count?>" align="left">
										<input type="hidden" name="action" value="update">
										<input type="hidden" name="delete_token" value="<?=Session::get("delete_token")["token"]?>">
										<input type="hidden" name="id" value="<?=$rec->id?>">
										<input type="hidden" name="model" value="<?=$em->getModelName()?>">
										<button type="submit" class="btn btn-success"><?=__("button-save")?></button>
										<button type="reset" class="btn btn-danger" onclick="$('.input-value').hide();$('.display-value').show();return false;"><?=__("button-cancel")?></button>&nbsp;
								</td>
							</tr>
						</form>
<?php
						}
					}else{
						foreach( $this->data_ext[$fld] as $index=>$rec){
?>
						<tr>
							<?php foreach( $field_list as $f ){ ?>
							<td width="<?=100/$count?>%">
								<?=$em->getFieldValue($f, $index)?>
							</td>
							<?php } ?>
						</tr>
<?php
						}
					}
				}
?>
						<form method="post" name="form-<?=$fld?>-add-new collapse" onsubmit="processForm(this); return false;">								
							<tr class="<?=$fld?>-add-new collapse">
								<?php foreach( $field_list as $f){ ?>
								<td class="input-value"><?php $em->fieldInputHTML($f); ?></td>
								<?php } ?>
							</tr>
							<tr class="<?=$fld?>-add-new collapse">
								<td colspan="<?=$count?>" align="left">
									<input type="hidden" name="action" value="create">
									<input type="hidden" name="model" value="<?=$em->getModelName()?>">
									<button type="submit" class="btn btn-success"><?=__("button-save")?></button>
									<button type="reset" class="btn btn-danger" onclick="$('tr.<?=$fld?>-add-new').hide();$('.empty-row').show();"><?=__("button-cancel")?></button>&nbsp;
								</td>
							</tr>
						</form>
					</tbody>
				</table>
			</p>
<?php
			}
		}		
		
	}