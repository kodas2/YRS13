<?php
	$product = "chrome book";
	$response = http_get("https://www.amazon.co.uk/s/field-keywords=" . $product);
	if ($response) {
		$dom = new DOMDocument();
		$dom->loadHTML($response);
		$dom->saveHTML();
		$linkToPageElement = $dom->getElementByClassName("newaps")[0];
		$linkToPage = linkToPageElement->getAttribute("href");
		$secondResponse = http_get(linkToPage);
		if ($secondResponse) {
			$secondDom = new DOMDocument();
			$secondDom->loadHTML($secondResponse);
			$secondDom->saveHTML();
			$buyingElements = $secondDom->getElementByClassName("buying");
			$xpath = new DOMXPath($secondDom);
			foreach($element in $buyingElements) {
				//Gets the correct div, which contains the title
				if ($xpath->query('*', $element)[0]->getAttribute("class") == "parseasinTitle") {
					echo $xpath->query('*', $element)[1]->nodeValue;
					break;
				}
			}
		}
	}
?>