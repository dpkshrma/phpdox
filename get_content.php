<?php

/**
 * PHPDOX -- command line PHP documentation tool
 * ---------------------------------------------
 * @uthor Deepak Sharma
 * @email deepak.sh406@gmail.com
 * ---------------------------------------------
 */

require_once dirname(__FILE__)."/phpclicolors.php";
/**
 * Class to search and display the documentation
 * of required function
 */
class PhpDox
{
    /**
     * @var string containing the new line character
     */
    protected static $lineBreak = "\n";
    /**
     * @var string containing the name of the file to
     * be searched
     */
    protected $fileName;
    /**
     * @var object to display colored messages on the console
     */
    protected $colorStringObj;
    public function __construct($searchString = null)
    {
        /**
         * Check if search string is passed in the constructor
         */
        if ($searchString === null) {
            throw new Exception("Search string is missing");
        }
        /**
         * Turn off the error reporting
         */
        error_reporting(0);
        /**
         * Disable XML errors
         */
        libxml_use_internal_errors(true);
        /**
         * Set the line break character
         */
        self::$lineBreak = "\n";
        /**
         * Set the file name according to the naming
         * convention of the HTML function files
         */
        $this->fileName = "function.".preg_replace('/_/','-',$searchString).".html";
        /**
         * Initialize object of colors class
         */
        $this->colorStringObj = new Colors();
    }
    /**
     * Function to check if file exists for a given function
     *
     * @return bool true if file of function exists
     */
    protected function fileExists()
    {
        if (file_exists(dirname(__FILE__)."/functions/".$this->fileName)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Echo the usage instruction on the console
     */
    public static function printUsageIns()
    {
        echo "Usage: phpdox <function_name>".self::$lineBreak;
    }
    /**
     * Function to echo the result to the terminal
     */
    public function printResult()
    {
        if (!$this->fileExists()) {
            echo $this->colorStringObj->getColoredString("Function not found.", "red").self::$lineBreak;
        } else {
            $htmlContent = file_get_contents(dirname(__FILE__)."/functions/".$this->fileName);
            $dom = new DOMDocument();
            $dom->loadHTML($htmlContent);
            $xpath = new DOMXPath($dom);
            $functionName = $xpath->query('//h1[@class="refname"]');
            $functionVersionInfo = $xpath->query('//p[@class="verinfo"]');
            $functionTitle = $xpath->query('//span[@class="dc-title"]');
            $functionDesc = $xpath->query('//div[@class="methodsynopsis dc-description"]');
            $functionDescAlias = $xpath->query('//p[@class="simpara"]');
            $functionRdfsComment = $xpath->query('//p[@class="para rdfs-comment"]');
            $functionParams = $xpath->query('//dt');
            $functionParamDesc = $xpath->query('//dd');
            $functionReturnVal = $xpath->query('//div[@class="refsect1 returnvalues"]/p[@class="para"]');
            echo $this->colorStringObj->getColoredString(
                    trim(strip_tags($functionName->item(0)->nodeValue)),
                    "light_red"
                ).self::$lineBreak;
            echo $this->colorStringObj->getColoredString(
                    " -- ".trim(strip_tags($functionTitle->item(0)->nodeValue)),
                    "white"
                ).self::$lineBreak;
            echo self::$lineBreak.$this->colorStringObj->getColoredString("Version info", "light_blue").self::$lineBreak;
            echo $this->colorStringObj->getColoredString(
                    trim(strip_tags($functionVersionInfo->item(0)->nodeValue)),
                    "light_gray"
                ).self::$lineBreak;
            echo self::$lineBreak.$this->colorStringObj->getColoredString("Description", "light_blue").self::$lineBreak;
            if ($functionDesc->item(0) != null) {
                echo $this->colorStringObj->getColoredString(
                        trim(strip_tags(preg_replace('/\s+/', ' ', $functionDesc->item(0)->nodeValue))),
                        "light_gray"
                    ).self::$lineBreak;
            }
            if ($functionDescAlias->item(0)!=null) {
                echo $this->colorStringObj->getColoredString(
                        trim(strip_tags(preg_replace('/\s+/', ' ', $functionDescAlias->item(0)->nodeValue))),
                        "light_gray").self::$lineBreak;
            }
            if (is_object($functionRdfsComment)) {
                echo $this->colorStringObj->getColoredString(
                        trim(strip_tags($functionRdfsComment->item(0)->nodeValue)),
                        "light_gray").self::$lineBreak;
            }
            echo self::$lineBreak.$this->colorStringObj->getColoredString("Parameters", "light_blue").self::$lineBreak;
            $par = array();
            $par_desc = array();
            foreach ($functionParams as $parameter) {
                $param = $parameter->nodeValue;
                $par[] = preg_replace('/\s+/', ' ',$param);
            }
            foreach ($functionParamDesc as $parameter_desc) {
                $param_desc = $parameter_desc->nodeValue;
                $par_desc[] = preg_replace('/\s+/', ' ',$param_desc);
            }
            for ($i=0; $i <count($par) ; $i++) {
                echo $this->colorStringObj->getColoredString(strip_tags($par[$i]),"green").self::$lineBreak;
                echo $this->colorStringObj->getColoredString(strip_tags($par_desc[$i]),"light_gray").self::$lineBreak.self::$lineBreak;
            }
            echo $this->colorStringObj->getColoredString("Return Value", "light_blue").self::$lineBreak;
            echo $this->colorStringObj->getColoredString(
                    trim(strip_tags($functionReturnVal->item(0)->nodeValue)),
                    "light_gray"
                ).self::$lineBreak;
        }
    }
}
if (!isset($argv[1])) {
    PhpDox::printUsageIns();
} else {
    $docObject = new PhpDox($argv[1]);
    $docObject->printResult();
}