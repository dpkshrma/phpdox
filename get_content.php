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

if (PHP_SAPI == "cli")
    $lb = "\n";
else
    $lb = "<br />";

$objectForColors = new Colors();

$file_name = $argv[1];
$file_name = preg_replace('/_/','-',$file_name);
$file_name = "function.".$file_name.".html";
$file_exists = false;

if (file_exists(dirname(__FILE__).'/functions/'.$file_name))
    $file_exists = true;

echo $lb;

if ($argv[1] == null || $argv[1] == '') {
    echo "Usage : phpdox <function_name>".$lb;
    echo $lb;
    die();
}

if (!$file_exists && $argv[1] != '' && $argv[1] != null ) {
    echo $objectForColors->getColoredString("Requested function does not exist in the database","light_red").$lb;
    echo $lb;
    die();

    // TODO : Suggest similar functions
    /*	foreach (new DirectoryIterator(dirname(__FILE__).'/functions/') as $file) {
            if($file->isDot()) break;
            $file_name = $file->getFilename()."$lb";
        }*/
}
else {
    $file = $file_name;


    $html = file_get_contents(dirname(__FILE__)."/functions/".$file);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $className="refname";
    $functionName = $xpath->query('//h1[@class="refname"]');
    $functionVeriInfo = $xpath->query('//p[@class="verinfo"]');
    $functionDCTitle = $xpath->query('//span[@class="dc-title"]');

    //function syntax
    $functionDescription = $xpath->query('//div[@class="methodsynopsis dc-description"]');
    $functionDescriptionAlias = $xpath->query('//p[@class="simpara"]');
    $functionRDFSComment = $xpath->query('//p[@class="para rdfs-comment"]');
    $functionParameters = $xpath->query('//dt');
    $functionParametersDescription = $xpath->query('//dd');
    $functionReturnValues = $xpath->query('//div[@class="refsect1 returnvalues"]/p[@class="para"]');

    // $fn_example_titles = $xpath->query('//div[@class="example"]/p/strong');
    // $fn_example_desc = $xpath->query('//div[@class="example-contents"]');

    echo $objectForColors->getColoredString(trim(strip_tags($functionName->item(0)->nodeValue)),"light_red");
    echo $lb.$objectForColors->getColoredString(" -- ".trim(strip_tags($functionDCTitle->item(0)->nodeValue)),"white")."$lb";
    echo $lb.$objectForColors->getColoredString("Version info","light_blue").$lb;
    echo $objectForColors->getColoredString(trim(strip_tags($functionVeriInfo->item(0)->nodeValue)),"light_gray").$lb;
    echo $lb.$objectForColors->getColoredString("Description","light_blue").$lb;

    if ($functionDescription->item(0) != null) {
        echo $objectForColors->getColoredString(trim(strip_tags(preg_replace('/\s+/', ' ',$functionDescription->item(0)->nodeValue))),"light_gray")."$lb";
    }
    if ($functionDescriptionAlias->item(0)!=null) {
        echo $objectForColors->getColoredString(trim(strip_tags(preg_replace('/\s+/', ' ',$functionDescriptionAlias->item(0)->nodeValue))),"light_gray")."$lb";
    }

    if (is_object($functionRDFSComment)) {
        echo $objectForColors->getColoredString(trim(strip_tags($functionRDFSComment->item(0)->nodeValue)),"light_gray")."$lb";
    }

    echo $lb.$objectForColors->getColoredString("Parameters","light_blue").$lb;

    $par = array();
    $par_desc = array();

    foreach ($functionParameters as $parameter) {
        $param = $parameter->nodeValue;
        $par[] = preg_replace('/\s+/', ' ',$param);
    }
    foreach ($functionParametersDescription as $parameter_desc) {
        $param_desc = $parameter_desc->nodeValue;
        $par_desc[] = preg_replace('/\s+/', ' ',$param_desc);
    }
    for ($i=0; $i <count($par) ; $i++) {
        echo $objectForColors->getColoredString(strip_tags($par[$i]),"green").$lb;
        echo $objectForColors->getColoredString(strip_tags($par_desc[$i]),"light_gray").$lb.$lb;
    }
    echo $objectForColors->getColoredString("Return Value","light_blue").$lb;
    echo $objectForColors->getColoredString(trim(strip_tags($functionReturnValues->item(0)->nodeValue)),"light_gray")."$lb";
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