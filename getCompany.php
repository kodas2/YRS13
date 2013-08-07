<?php
	function getCompanyName($inputtedProductName, $noOfResult=0) {
		$product = $inputtedProductName;
		$companyName = FALSE;
		$response = file_get_contents("https://www.amazon.co.uk/s/field-keywords=" . $product);
		if ($response !== FALSE) {
			$dom = new DOMDocument();
			$dom->loadHTML($response);
			$linkToPage = $dom->getElementById("result_" . $noOfResult)->getElementsByTagName("a")->item(0)->getAttribute("href");

			$secondResponse = file_get_contents($linkToPage);
			if ($secondResponse) {
				$secondDom = new DOMDocument();
				$secondDom->loadHTML($secondResponse);
				$count = 0;
				foreach ($secondDom->getElementsByTagName("a") as $aLink) {
					if ($aLink->parentNode->getAttribute("class") == "buying") {
						$companyName = $aLink->nodeValue;
						break;
					}
				}
			}
		}
		return $companyName;
	}

	function getStockSymbol($companyName) {
		$JSONresponse = file_get_contents("http://d.yimg.com/autoc.finance.yahoo.com/autoc?query=" . $companyName . "&callback=YAHOO.Finance.SymbolSuggest.ssCallback");
		//TODO: parse JSON with Ed's parser
	}

	function getListCompanyNames($inputtedProductName, $noOfResults) {
		$listOfCompanyNames = array();
		$possibleDupes = array();

		for ($i = 0; $i < $noOfResults; $i++) {
			//TODO: don't keep fetching the same dat in getCompanyName() 
			array_push($listOfCompanyNames, getCompanyName($inputtedProductName, $i));
		}

		for ($j = 0; $j < $noOfResults; $j++) {
			for ($k = 0; $k < $noOfResults; $k++) {
				$wasDupe = False;
				if ($possibleDupes[$k] != NULL) {
					if ($possibleDupes[$k] == $listOfCompanyNames[$j]) {
						unset($listOfCompanyNames[$j]);
						$listOfCompanyNames = array_values($listOfCompanyNames);
						$wasDupe = True;
					}
				} elseif (!$wasDupe) {
					//echo $possibleDupes[$k] . ":" . $listOfCompanyNames[$j];
					array_push($possibleDupes, $listOfCompanyNames[$j]);
					$shouldBreak = True;
				}
				/*echo "<br></br> possibleDupes = " . var_dump($possibleDupes) . "<br></br>";
				echo "j = " . $j . "<br></br>";
				echo "k = " . $k . "<br></br>";
				echo "wasDupe = " . $wasDupe . "<br></br>";
				echo "listOfCompanyNames = " . var_dump($listOfCompanyNames) . "<br></br>";
				echo "possibleDupes[k] = " . $possibleDupes[$k] . "<br></br>";
				echo "listOfCompanyNames[j] = " . $listOfCompanyNames[$j] . "<br></br>";*/

				if ($shouldBreak) {
					break;
				}
			}
		}

		return $listOfCompanyNames;
	}
?>