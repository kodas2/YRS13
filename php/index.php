<?php
$companysymbol = "";
if((isset($_POST['stockname']))) {
	$companysymbol = $_POST['stockname'];	
} else {
	$companysymbol = "AAPL";
}

echo '<script src="Chart.js"></script>';

$stockQuotes = simplexml_load_file('http://www.google.com/ig/api?stock=' . $companysymbol);
$companyname = "";

foreach ($stockQuotes as $Acc_info):
	$companyname = (string)$Acc_info->company['data'];
endforeach;

echo <<<_END
			<form method='post' action='index.php'>
			<textarea name="stockname" cols="20" rows="1" wrap="hard" maxlength="100"></textarea></br>
			<input type='submit' value='search' /></form>
_END;

echo 'Company symbol : ' . $companysymbol . '</br>';
echo 'Company name : ' . $companyname . '</br>';

$historystock = file_get_contents("http://ichart.yahoo.com/table.csv?s=" . $companysymbol . "&a=0&b=1&c=2000&d=7&e=11&f=2013&g=w&ignore=.csv");

$historystock = str_replace("\n", "," , $historystock);
$historystock = explode(",", $historystock);

for($i=0; $i<count($historystock)-1; $i+=7) {
	echo '<table border="1">';
	echo '<tr>';
	echo '<td>';
	echo $historystock[$i + 0] .'&nbsp;';
	echo '</td>';
	echo '<td>';
	echo $historystock[$i + 6] .'&nbsp;';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
}
?>