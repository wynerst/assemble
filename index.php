<?php

function multipleEntry($elementXML) {
	if (is_null($elementXML->length)) {
		return 0;
	} else {
		$count=$elementXML->length;
		$textContent = ""; $i=1; 
		foreach ($elementXML as $tc) {
			$textContent .= $i.". ". $tc->nodeValue ." \n";
			$i++;
		}
		return $textContent;
	}
}

if (isset($_GET["ojs"])) {
	$oai = $_GET["ojs"];
	if (isset($_GET["resumptionToken"])) {
		$nextToken = $_GET["resumptionToken"];
		// https://ijdc.net/index.php/ijdc/oai?verb=ListRecords&resumptionToken=b021e917d1f9be0991d6216a45d724c0
		$url=$oai.'/oai?verb=ListRecords&resumptionToken='.$nextToken;
	} else {
		// https://ijdc.net/index.php/ijdc/oai?verb=ListRecords&metadataPrefix=oai_dc
		$url=$oai.'/oai?verb=ListRecords&metadataPrefix=oai_dc';
	}

/** Processing XML Element **/
/**	Example URL
	$oai = "https://ijdc.net/index.php/ijdc";
	//$oai = 'https://academicjournal.yarsi.ac.id/index.php/bibliotech';
	$url = $oai.'/oai?verb=ListRecords&metadataPrefix=oai_dc';
}
**/
echo 'Fetching contents from URL: ' . $url ."\n";

$contents = file_get_contents($url);

$doc = new DOMDocument();
$doc->loadXML($contents);

$records = $doc->getElementsByTagName('record');

foreach ($records as $record) {
  $metadata = $record->getElementsByTagName('metadata')->item(0);

  // Access child elements within "metadata" using namespaces
  $title = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'title')->item(0)->textContent;
  $creator= multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'creator'));
  $description = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'description')->item(0)->textContent;
  $publisher = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'publisher')->item(0)->textContent;
  $date = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'date')->item(0)->textContent;
  $type = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'type'));
  $format = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'format')->item(0)->textContent;
  $identifier = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'identifier'));
  $source = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'source'));
  $language = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'language')->item(0)->textContent;
  $relation = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'relation')->item(0)->textContent;
  
  // Process the extracted text content

  echo "Title: $title\n";
  echo "Creator: $creator";
  echo "Description: $description\n";
  echo "publisher: $publisher\n"; 
  echo "date: $date\n";
  echo "type: $type";
  echo "format: $format\n";
  echo "identifier: $identifier";
  echo "source: $source"; 
  echo "language: $language\n"; 
  echo "relation:   $relation\n\n";
  
}
if ($doc->getElementsByTagName('resumptionToken')->length > 0) {
	$resume=$doc->getElementsByTagName('resumptionToken')->item(0)->textContent;
	// 	echo 'Resumption token = <a href="https://ijdc.net/index.php/ijdc/oai?verb=ListRecords&resumptionToken='.$resume.'">Lanjut</a>';
	echo '<a href="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?ojs='.$oai.'&resumptionToken='.$resume.'">Load more data</a>';
}

} else {
	
echo '<form method="GET">';
echo 'OJS URL: <input type="text" name="ojs">&nbsp;&nbsp;';
echo '<input type="submit">';
echo '</form>';
}

?>