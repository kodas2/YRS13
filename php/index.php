<?php
$response = http_get("http://d.yimg.com/autoc.finance.yahoo.com/autoc?query=google&&callback=YAHOO.Finance.SymbolSuggest.ssCallback", array("timeout"=>1), $info);
print_r($info);

echo <<_END
<html>
<head>
</head>
</html>
_END;
?>