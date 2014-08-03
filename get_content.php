<?php

/**
 * PHPDOX -- command line PHP documentation tool
 * ---------------------------------------------
 * @uthor Deepak Sharma
 * @email deepak.sh406@gmail.com
 * ---------------------------------------------
 */

ini_set('display_errors', false);
error_reporting(0);

require_once dirname(__FILE__)."/phpclicolors.php";

$j=0;

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";

$colors = new Colors();

$file_name = $argv[1];
$file_name = preg_replace('/_/','-',$file_name);
$file_name = "function.".$file_name.".html";
$file_exists = false;

if ( file_exists(dirname(__FILE__).'/functions/'.$file_name) ) {
	$file_exists = true;
}

echo $lb;

if ($argv[1] == null || $argv[1] == '') {
	echo "Usage : phpdox <function_name>".$lb;
	echo $lb;
	die();
}

if (!$file_exists && $argv[1] != '' && $argv[1] != null ) {
	echo $colors->getColoredString("Requeted function does not exist in the database","light_red").$lb;
	echo $lb;
	die();

	// TODO : Suggest similar functions
/*	foreach (new DirectoryIterator(dirname(__FILE__).'/functions/') as $file) {
		if($file->isDot()) break;
		$file_name = $file->getFilename()."$lb";
	}*/
}
else{
	$file = $file_name;


	$html = file_get_contents(dirname(__FILE__)."/functions/".$file);

	$dom = new DOMDocument();
    libxml_use_internal_errors(true);
	$dom->loadHTML($html);
	$xpath = new DOMXPath($dom);

	$classname="refname";
	$fn_name = $xpath->query('//h1[@class="refname"]');
	$fn_verinfo = $xpath->query('//p[@class="verinfo"]');
	$fn_dctitle = $xpath->query('//span[@class="dc-title"]');

	//function syntax
	$fn_desc = $xpath->query('//div[@class="methodsynopsis dc-description"]');
	$fn_desc_alias = $xpath->query('//p[@class="simpara"]');
	$fn_rdfscomment = $xpath->query('//p[@class="para rdfs-comment"]');
	$fn_parameters = $xpath->query('//dt');
	$fn_parameters_desc = $xpath->query('//dd');	
	$fn_returnvals = $xpath->query('//div[@class="refsect1 returnvalues"]/p[@class="para"]');

	// $fn_example_titles = $xpath->query('//div[@class="example"]/p/strong');
	// $fn_example_desc = $xpath->query('//div[@class="example-contents"]');

	echo $colors->getColoredString(trim(strip_tags($fn_name->item(0)->nodeValue)),"light_red");
	echo $lb.$colors->getColoredString(" -- ".trim(strip_tags($fn_dctitle->item(0)->nodeValue)),"white")."$lb";
	echo $lb.$colors->getColoredString("Version info","light_blue").$lb;
	echo $colors->getColoredString(trim(strip_tags($fn_verinfo->item(0)->nodeValue)),"light_gray").$lb;
	echo $lb.$colors->getColoredString("Description","light_blue").$lb;
	if ($fn_desc->item(0) != null) {
		echo $colors->getColoredString(trim(strip_tags(preg_replace('/\s+/', ' ',$fn_desc->item(0)->nodeValue))),"light_gray")."$lb";
	}
	if ($fn_desc_alias->item(0)!=null) {
		echo $colors->getColoredString(trim(strip_tags(preg_replace('/\s+/', ' ',$fn_desc_alias->item(0)->nodeValue))),"light_gray")."$lb";
	}

	if (is_object($fn_rdfscomment)) {
		echo $colors->getColoredString(trim(strip_tags($fn_rdfscomment->item(0)->nodeValue)),"light_gray")."$lb";
	}

	echo $lb.$colors->getColoredString("Parameters","light_blue").$lb;
	$par = array();
	$par_desc = array();
	foreach ($fn_parameters as $parameter) {
		$param = $parameter->nodeValue;
		$par[] = preg_replace('/\s+/', ' ',$param);
	}
	foreach ($fn_parameters_desc as $parameter_desc) {
		$param_desc = $parameter_desc->nodeValue;
		$par_desc[] = preg_replace('/\s+/', ' ',$param_desc);
	}
	for ($i=0; $i <count($par) ; $i++) { 
		echo $colors->getColoredString(strip_tags($par[$i]),"green").$lb;
		echo $colors->getColoredString(strip_tags($par_desc[$i]),"light_gray").$lb.$lb;
	}
	echo $colors->getColoredString("Return Value","light_blue").$lb;
	echo $colors->getColoredString(trim(strip_tags($fn_returnvals->item(0)->nodeValue)),"light_gray")."$lb";
	// TODO : Option to view Examples
/*	echo "8 Examples : $lb";
	$eg_titles = array();
	$eg_desc = array();
	foreach ($fn_example_titles as $title) {
		$eg_titles[] = $title->nodeValue;
	}
	foreach ($fn_example_desc as $desc) {
		$eg_desc[] = $desc->nodeValue;
	}
	for ($i=0; $i <count($eg_titles) ; $i++) { 
		echo "<b>".trim(strip_tags($eg_titles[$i])).$lb."</b>";	
		echo $eg_desc[$i].$lb;	
	}*/
}
echo "$lb";