<?php
//1234567890
//@TEST4632 
//testword
///Consumer key 	KGDHjmzWnQCKk8KLGMEEQ
//Consumer secret 	VuG4UH2GPq4BjPoruRSZ6cUYCbM5rncXz35g6ZyiQ
//Request token URL 	https://api.twitter.com/oauth/request_token
//Authorize URL 	https://api.twitter.com/oauth/authorize
//Access token URL 	https://api.twitter.com/oauth/access_token
//Access token	        1647806779-ixm0ntlbfvzzFvRNJIre9Yr2OPzx6LDPEPTciq7
//Access token secret 	UMbprheGu19qxV6KpN2Mw3XRvAlAAbqQZiFBdfeaM

$AuthRequest=array();
$AuthRequest[0]="Authorization: Basic S0dESGptelduUUNLazhLTEdNRUVROlZ1RzRVSDJHUHE0QmpQb3J1UlNaNmNVWUNiTTVybmNYejM1ZzZaeWlR";
$AuthRequest[1]="Content-Type: application/x-www-form-urlencoded;charset=UTF-8";

$AuthField="grant_type=client_credentials";
$Resource=curl_init("https://api.twitter.com/oauth2/token");
curl_setopt($Resource, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($Resource, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($Resource, CURLOPT_HTTPHEADER,$AuthRequest);
curl_setopt($Resource, CURLOPT_POST,1);
curl_setopt($Resource,CURLOPT_POSTFIELDS, $AuthField);

curl_setopt($Resource, CURLOPT_RETURNTRANSFER,1); 
$result=curl_exec($Resource);
if($result==FALSE)die("");
else
{
$data=json_decode($result,true);
if($data==false)die("");

if($data["token_type"]=="bearer")
{
$AuthorizationHeader=array();
$AuthorizationHeader[0]="Authorization: Bearer ".$data["access_token"];
$Query=urlencode("\"".$_GET["q"]."\" lang:en");
$Resource=curl_init("https://api.twitter.com/1.1/search/tweets.json?q=$Query");
curl_setopt($Resource, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($Resource, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($Resource, CURLOPT_HTTPHEADER,$AuthorizationHeader);
curl_setopt($Resource, CURLOPT_RETURNTRANSFER,1); 
$result=curl_exec($Resource);
$Tweets=json_decode($result,true);
if($Tweets==false)die("");
$Data=array();
for($i=0;$i<count($Tweets["statuses"]);$i++)
{
$Data[$i]=$Tweets["statuses"][$i]["text"];
}
echo json_encode($Data);
}
else
{
die("");
};
}
?>
