<?php
	// class to generate Excel files
	
		
	class Excel{

		protected $xml;
	
		public function __construct(){
			$this->xml = new DOMDocument;
			$this->xml->loadXML(
'<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
</Workbook>');
		}
			
		public function saveXML(){
			return $this->xml->saveXML();
		}
		
		public function addWorksheet($name){
			$worksheet = $this->xml->createElement("Worksheet");
			$worksheet->setAttribute("ss:Name",$name);
			$workbook  = $this->xml->getElementsByTagName("Workbook");
			$workbook->item(0)->appendChild($worksheet);
			$table = $this->xml->createElement("Table");
			$worksheet->appendChild($table);
			return $table;
		}
		
		public function addRow($table_node){
			$row = $this->xml->createElement("Row");
			$table_node->appendChild($row);
			return $row;
		}
		
		public function addCellData($row_node,$type,$value){
			$cell = $this->xml->createElement("Cell");
			$row_node->appendChild($cell);
			$data = $this->xml->createElement("Data",$value);
			$data->setAttribute("ss:Type",$type);
			$cell->appendChild($data);
		}
		
		public function xlsDownload($name){
			header("Content-Type: application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=$name.xls");

			echo $this->xml->saveXML();
			die();
		}
		
		
	}