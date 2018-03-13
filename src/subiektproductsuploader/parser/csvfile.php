<?php
namespace SubiektProductsUploader\Parser;

class CsvFile{

	protected $data = array();

	public function __construct($file_name){
		if (($handle = fopen($file_name, "r")) !== FALSE) {			
		    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		        array_push($this->data,$data);
		    }
		    fclose($handle);
		}
	}

	public function getRow(){
		foreach($this->data as $key => $value){

			yield $value;

		}
	}

	public function count(){
		return count($this->data);
	}

}
?>