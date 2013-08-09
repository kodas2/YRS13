<?php
$companyname = "";
$product = "";
date_default_timezone_set("UTC");
if((isset($_GET['stockname']))) {
	$companyname = $_GET['stockname'];	
} else {
	$companyname = "apple";
}
$product = "";
if((isset($_GET['product']))) {
	$product = $_GET['product'];
}
$topsyapijson = file_get_contents("http://otter.topsy.com/searchhistogram.json?q=kindle&period=357&slice=265000&apikey=OBTUFOILBGJS73J2PUMAAAAAACKMHDQSFJJAAAAAAAAFQGYA");
$topsyapi = json_decode($topsyapijson, true);
$topsyarray = array();

foreach($topsyapi['response']['histogram'] as $topsy) :
	array_push($topsyarray, $topsy);
endforeach;
/*
$product = str_replace(" ", "%20", $product);
//$newsstories = file_get_contents("https://www.google.com/trends/fetchComponent?q=" . $product . "&cid=TIMESERIES_GRAPH_0&export=3");
$popularity = file_get_contents("chromebook.txt");
$popularity = substr($popularity, 63, -2);
$popularity = str_replace("new Date(", '"', $popularity);
$popularity = str_replace(")", '"', $popularity);

$popularityjson = json_decode($popularity, true);
foreach($popularityjson['table']['rows'] as $c) :
	echo $c['c'][0]['v'] . " , ";
	echo $c['c'][1]['v'] . "  ,  ";
endforeach;
*/

/*
echo <<<_END
	<script src="jquery-2.0.3.min.js"></script>
	<script src="Chart.js"></script>
_END;
*/

$companydatajson = substr(file_get_contents("http://d.yimg.com/autoc.finance.yahoo.com/autoc?query=" . $companyname ."&&callback=YAHOO.Finance.SymbolSuggest.ssCallback"), 39,-1);
$companydata = json_decode($companydatajson, true);
$companysymbol = $companydata['ResultSet']['Result'][0]['symbol'];

/*
echo <<<_END
		<html>
		<head>
		<link href="style.css" rel="stylesheet" type="text/css">
		<title>Money Vs People</title>
		<div id="container">	
		<div id="searchbar">
		<form method='post' action='index.php'>
		<textarea name="stockname" cols="20" rows="1" wrap="hard" maxlength="100"></textarea></br>
		<input type='submit' value='search'/></form>
		</div>
		
		<div id="graph">
_END;

echo 'Company symbol : ' . $companysymbol . '</br>';
echo 'Company name : ' . $companyname . '</br>';
*/

$historystock = file_get_contents("http://ichart.yahoo.com/table.csv?s=" . $companysymbol . "&a=0&b=1&c=2010&d=7&e=11&f=2013&g=w&ignore=.csv");

$historystock = str_replace("\n", "," , $historystock);
$historystock = explode(",", $historystock);

$dates = array("");
$prices = array("");

for($i=0; $i<count($historystock)-1; $i+=14) {
	array_unshift($dates, $historystock[$i + 0]);
	array_unshift($prices, $historystock[$i + 6]);
}
echo count($dates) . ', ';
echo count($topsyarray) . ', ';
$highestprice = 0;
foreach($prices as $price) :
	if($price > $highestprice && $price != "Adj Close") $highestprice = $price;
endforeach;
$highestprice = $highestprice/10;
for($i=0; $i<count($dates)-1; $i+=1) {
	if($i%12!=0) $dates[$i]="_"; else $dates[$i] = substr($dates[$i], 0, 4);
}
/*
echo <<<_END
	<canvas id="myChart" width="1300" height="800"></canvas>
	
	<script>
		var ctx = document.getElementById("myChart").getContext("2d");
		*/
echo <<<_END
		var data = {
			labels : [
_END;
		foreach($dates as $date) :
					if($date != "Date" && $date != "") {
						if($date == end($dates)) {
							echo '"' . $date . '"';
						} else {
							echo '"' . $date . '"' . ",";
						}
					}
		endforeach;
		echo'],';
echo <<<_END
			datasets : [
				{
					fillColor : "rgba(151,187,205,0.3)",
					strokeColor : "rgba(255,255,255,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : [
_END;
		foreach($prices as $price) :
					if($price != "Adj Close" && $price != "") {
						if($price == end($prices)) {
							echo $price;
						} else {
							echo $price . ",";
						}
					}
		endforeach;
		echo']},';
		
		echo <<<_END
				{
					fillColor : "rgba(151,187,205,0.3)",
					strokeColor : "rgba(255,255,255,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : [
_END;
		foreach($topsyarray as $topsy) :
				if($topsy == $topsyarray[count($topsyarray)-1]) {
					echo $topsy;
				} else {
					echo $topsy . ",";
				}
		endforeach;
		echo']}]}';
		/*
echo <<<_END
				}
			]
		}
		var options = {			
		//Boolean - If we show the scale above the chart data			
		scaleOverlay : true,
		
		//Boolean - If we want to override with a hard coded scale
		scaleOverride : true,
		
		//** Required if scaleOverride is true **
		//Number - The number of steps in a hard coded scale
		scaleSteps : 10,
		//Number - The value jump in the hard coded scale
		scaleStepWidth : 
_END;
		echo $highestprice;
echo <<<_END
		,
		//Number - The scale starting value
		scaleStartValue : 0,

		//String - Colour of the scale line	
		scaleLineColor : "rgba(255,255,255,1)",
		
		//Number - Pixel width of the scale line	
		scaleLineWidth : 3,

		//Boolean - Whether to show labels on the scale	
		scaleShowLabels : true,
		
		//Interpolated JS string - can access value
		scaleLabel : "<%=value%>",
		
		//String - Scale label font declaration for the scale label
		scaleFontFamily : "'Arial'",
		
		//Number - Scale label font size in pixels	
		scaleFontSize : 12,
		
		//String - Scale label font weight style	
		scaleFontStyle : "normal",
		
		//String - Scale label font colour	
		scaleFontColor : "#666",	
		
		///Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines : true,
		
		//String - Colour of the grid lines
		scaleGridLineColor : "rgba(0,0,0,.07)",
		
		//Number - Width of the grid lines
		scaleGridLineWidth : 40,	
		
		//Boolean - Whether the line is curved between points
		bezierCurve : false,
		
		//Boolean - Whether to show a dot for each point
		pointDot : false,
		
		//Number - Radius of each point dot in pixels
		pointDotRadius : 3,
		
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth : 1,
		
		//Boolean - Whether to show a stroke for datasets
		datasetStroke : true,
		
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth : 3,
		
		//Boolean - Whether to fill the dataset with a colour
		datasetFill : true,
		
		//Boolean - Whether to animate the chart
		animation : true,

		//Number - Number of animation steps
		animationSteps : 60,
		
		//String - Animation easing effect
		animationEasing : "easeOutQuart",

		//Function - Fires when the animation is complete
		onAnimationComplete : null
	}
	var MyNewChart = new Chart(ctx).Line(data, options);
	</script>
_END;

echo <<<_END
	</div>
	</head>
	</html>
_END;
*/
?>
