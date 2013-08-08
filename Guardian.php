<?php
require("parseJson.php");

	function queryYear($Company,$Product,$Year,&$FirstOne)
	{
	$NextYear=$Year+1;
	$URL="http://content.guardianapis.com/search?q=$Company%20$Product&from-date=$Year-01-01&to-date=$NextYear-01-01&format=json&api-key=dgzwv7yyec5yqmyu4zzgd794";
	$Resource=curl_init($URL);
	curl_setopt($Resource, CURLOPT_RETURNTRANSFER,1); 
	$result=curl_exec($Resource);
	$data=parseJSON($result);
		if($data==false)echo "ERROR";
		$json="";
		for($i=0;$i<count($data["response"]["results"]);$i++)
		{
		if(preg_match("#".$Company."#i",$data["response"]["results"][$i]["webTitle"])||preg_match("#".$Product."#i",$data["response"]["results"][$i]["webTitle"]))
			{
				if(!$FirstOne)
				{
				$json=$json.',';
				}
			$FirstOne=false;
			$json=$json."{title:\"";
			$json=$json.addcslashes($data["response"]["results"][$i]["webTitle"],'"\\\'');
			$json=$json."\",url:\"";
			$json=$json.$data["response"]["results"][$i]["webUrl"];
			$json=$json."\",date:\"";
			$json=$json.$data["response"]["results"][$i]["webPublicationDate"];
			$json=$json."\"}";
			}
		}
	return $json;
	}
date_default_timezone_set("UTC");
$Company=urlencode($_GET["company"]);
$Product=urlencode($_GET["product"]);
$FirstOne=true;
$json="[";
	for($Year=2000;$Year<=intval(date("Y"));$Year++)
	{
	$json=$json.queryYear($Company,$Product,$Year,$FirstOne);
	}
$json=$json."]";
echo $json;
?>
