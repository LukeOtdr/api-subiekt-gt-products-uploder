<?php

use SubiektProductsUploader\Config;
use SubiektProductsUploader\Parser\CsvFile;
use SubiektProductsUploader\SubiektApi;

require_once(dirname(__FILE__).'/init.php');

	//Config object	
	session_start();
	
	$cfg = new Config(CONFIG_INI_FILE);
	$cfg->load();

	if(isset($_POST['cancelfile']) && isset($_SESSION['products_file'])){
		unlink($_SESSION['products_file']);
		unset($_SESSION['products_file']);
	}
  
  $csvfile = false;
	$processing_response = array();
	if(isset($_POST['processfile']) && isset($_SESSION['products_file'])) {

		$csvfile = new CsvFile($_SESSION['products_file']);
		$api = new SubiektApi($cfg->getAPIKey(),$cfg->getEndPoint());
		foreach($csvfile->getRow() as $row){
      //var_dump($row);
			$product = array(
				'supplier_code' => $row[0],
				'code'=>$row[1],
        'ean'=>$row[2],
				'name' => $row[3], 	
        'name_for_devices' => $row[4],									
        'description' => $row[5],
				'wholesale_price' => $row[6],
				'price' => $row[7],
        'vat' => $row[8],
        'weight' => $row[9],
        'capacity' => $row[10],
        'supplier_id' => $row[11],
        'time_of_delivery' => $row[12],  
        'intrastat_code'=>!empty($row[13])?$row[13]:null,
        'intrastat_country_id'=>!empty($row[14])?$row[14]:null,
        'attribute'=>!empty($row[15])?$row[15]:null,
        'off_prefix' => 1
				);
		    
        /*
        'time_of_delivery' => $row[6],  
        'group_id' => $row[7], 
        'intrastat_code'=>!empty($row[8])?$row[8]:null,
        'intrastat_country_id'=>!empty($row[9])?$row[9]:null,
        */
        //echo '<pre>';
        $is_exists = $api->call('product/isexists',$product);
        //var_dump($is_exists);
        //ECHO $is_exists;
        if($is_exists['state'] == 'success'){
          if($is_exists['data'] == false) {
              $processing_response[$row[0]] =  $api->call('product/add',$product);
          }else{
              //echo "JEST";
              $processing_response[$row[0]] =  $api->call('product/update',$product);      
          }
        }
        $p =  $api->call('product/get',$product);  
        //var_dump($p);
		  //exit;  	
		}
		//TODO: usunać plik 
    //var_dump($processing_response);
		unlink($_SESSION['products_file']);
		unset($_SESSION['products_file']);
	}

	if(isset($_FILES['products_file'])){
		$csvfile = new CsvFile($_FILES['products_file']['tmp_name']);	
		$file_name = tempnam(sys_get_temp_dir(),'api-uploader');
		file_put_contents($file_name, file_get_contents($_FILES['products_file']['tmp_name']));
		$_SESSION['products_file'] = $file_name;
	}

	if(isset($_SESSION['products_file'])){		
		$csvfile = new CsvFile($_SESSION['products_file']);	
	}

	//var_dump($_SESSION['products_file']);

?>
<!DOCTYPE html>
<html lang="pl">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Wysłanie produktow</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.js" integrity="sha256-fNXJFIlca05BIO2Y5zh1xrShK3ME+/lYZ0j+ChxX2DA=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="js/jquery.api.uploader.js">
    
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
 
    <form enctype="multipart/form-data" method="post">
    <div class="row">
      <div class="twleve columns" style="margin-top:30px;">     
         <h4>Import produktów do subiekta</h4>     
	   <h5>Wybierz plik z produktami do zaimportowania do subiekta.</h5>	
	   <div>End point: <b><?php echo $cfg->getEndPoint();?></b></div>
      </div>
    </div>
  <div class="row">
  	 <div class="one-half column">
     		<label for="newprefix">Plik (csv)</label>
          <input class="u-full-width" name="products_file" type="file" placeholder="załaduj plik">
      </div> 
  </div>   
  <div class="row">
  	  <div class="one-half column">
     		<input class="button-primary" type="submit" value="Wyślij" name="save">     		
      </div>  
  </div>  
</form>
<?php if(count($processing_response)>0): ?>
    <div class="row">
    	<form method="post">
      <div class="twleve columns" style="margin-top:30px;"> 
      <h5>Informacja o imporcie produktów</h5> 
      <table style="width:100%;">
      	<thead>
      		<tr>
      			<td>SKU</td>
      			<td>Odpowiedź Subiekta</td>
      		</tr>
      	</thead>	
      	<tbody>
      		<?php foreach($processing_response as $code => $row): ?>
      			<tr>
      				<td ><?php echo $code ?></td>
      				<td><?php echo ($row['state']) ?></td>      				
      			</tr>
      		<?php endforeach; ?>
      	</tbody>
      </table>
      </div>

<?php endif;?>
<?php if($csvfile && $csvfile->count()>0 && count($processing_response)==0): ?>
    <div class="row">
    	<form method="post">
      <div class="twleve columns" style="margin-top:30px;"> 
      <h5>Weryfikacja wprowadzonego pliku</h5> 
      <table style="width:100%;">
      	<thead>
      		<tr>
      			<td>SKU</td>
      			<td>EAN</td>
            <td>Kod kreskowy</td>
      			<td>Nazwa</td>
            <td>Nazwa w urzą.</td>
            <td>Opis</td>
      			<td>Cena netto zakupu</td>
      			<td>Cena detal brutto</td>
            <td>st. VAT</td>
            <td>Masa</td>
            <td>Objętość</td>
      			<td>Id dostawycy</td>
      			<td>Czas realizacji</td>   
            <td>Kod intrastat</td>  			
            <td>Kod kraju pochodzenia</td> 
            <td>Cecha</td>
      		</tr>
      	</thead>
      	<tbody>
      		<?php foreach($csvfile->getRow() as $row): ?>
      			<tr id="<?php echo $row[0] ?>">
      				<td ><?php echo $row[0] ?></td>
      				<td><?php echo $row[1] ?></td>
      				<td><?php echo $row[2] ?></td>
      				<td><?php echo $row[3] ?></td>
      				<td><?php echo $row[4] ?></td>
      				<td><?php echo $row[5] ?></td>
      				<td><?php echo $row[6] ?></td>
              <td><?php echo $row[7] ?></td>
              <td><?php echo $row[8] ?></td>
              <td><?php echo $row[9] ?></td>
              <td><?php echo $row[10] ?></td>
              <td><?php echo $row[11] ?></td>
              <td><?php echo $row[12] ?></td>
              <td><?php echo !empty($row[13])?$row[13]:'' ?></td>
              <td><?php echo !empty($row[14])?$row[14]:'' ?></td>
              <td><?php echo !empty($row[15])?$row[15]:'' ?></td>
      			</tr>
      		<?php endforeach; ?>
      	</tbody>
      </table>
      </div>
        <div class="row">
	  	  <div class="one-half column">
	     		<input class="button-primary" type="submit" value="Zatwierdzam dane" name="processfile">  
	     		<input class="button-primary" type="submit" value="Usuwam plik" name="cancelfile">    		
	      </div>  
  	</div>  
  	</form>
     </div>
<?php endif; ?>     
</div>
<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>

