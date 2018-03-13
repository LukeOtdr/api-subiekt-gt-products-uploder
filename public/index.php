<?php
use SubiektProductsUploader\Config;
use SubiektProductsUploader\Parser\CsvFile;

require_once(dirname(__FILE__).'/init.php');

	//Config object	
	$cfg = new Config(CONFIG_INI_FILE);
	$cfg->load();

	$csvfile = new CsvFile(dirname(__FILE__).'/testdata.csv');

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
    
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
 
    <form method="post">
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
          <input class="u-full-width" name="newprefix" type="file" placeholder="załaduj plik">
      </div> 
  </div>   
  <div class="row">
  	  <div class="one-half column">
     		<input class="button-primary" type="submit" value="Wyślij" name="save">     		
      </div>  
  </div>  
</form>
<?php if($csvfile>0): ?>
    <div class="row">
    	<form>
      <div class="twleve columns" style="margin-top:30px;"> 
      <h5>Weryfikacja wprowadzonego pliku</h5> 
      <table>
      	<thead>
      		<tr>
      			<td>SKU</td>
      			<td>EAN</td>
      			<td>Nazwa</td>
      			<td>Cena netto</td>
      			<td>Cena detal brutto</td>
      			<td>Id dostawycy</td>
      			<td>Czas realizacji</td>
      		</tr>
      	</thead>
      	<tbody>
      		<?php foreach($csvfile->getRow() as $row): ?>
      			<tr>
      				<td><?php echo $row[0] ?></td>
      				<td><?php echo $row[1] ?></td>
      				<td><?php echo $row[2] ?></td>
      				<td><?php echo $row[3] ?></td>
      				<td><?php echo $row[4] ?></td>
      				<td><?php echo $row[5] ?></td>
      				<td><?php echo $row[6] ?></td>
      			</tr>
      		<?php endforeach; ?>
      	</tbody>
      </table>
      </div>
        <div class="row">
	  	  <div class="one-half column">
	     		<input class="button-primary" type="submit" value="Zatwierdzam dane" name="processfile">     		
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

