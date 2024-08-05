<?php
/**
** Harvesting OAI output from OJS - just catch and send the text
** © Wardiyono, 2024 - wynerst@gmail.com
**/

function multipleEntry($elementXML) {
	if (is_null($elementXML->length)) {
		return "";
	} else {
		$count=$elementXML->length;
		$textContent = ""; $i=1; 
		foreach ($elementXML as $tc) {
			$textContent .= $tc->nodeValue ."; ";
			$i++;
		}
		$textContent=preg_replace('/[; ]+$/', '', $textContent);
		return $textContent;
	}
}

if (isset($_GET["ojs"])) {
	$oai = rtrim($_GET["ojs"], '/\\');
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
  $deleted = $record->getElementsByTagName('header');
  if ($deleted[0]->getAttribute('status') == "deleted") {
	  continue;
  }
  $metadata = $record->getElementsByTagName('metadata')->item(0);

  // Access child elements within "metadata" using namespaces
  $title = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'title')->item(0)->textContent;
  $creator= multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'creator'));
  $description = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'description')->item(0)->textContent;
  $subject = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'subject'));
  $publisher = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'publisher')->item(0)->textContent;
  $date = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'date')->item(0)->textContent;
  $type = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'type'));
  $format = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'format')->item(0)->textContent;
  $identifier = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'identifier'));
  $source = multipleEntry($metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'source'));
  $language = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'language')->item(0)->textContent;
  $relation = $metadata->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'relation')->item(0)->textContent;
  
  // Process the extracted text content

  //echo "Title: $title\n";
  echo $title = (empty($title)) ? "" : "Title: $title\n";
  echo $creator = (empty($creator)) ? "" : "Creator: $creator\n";
  echo $subject = (empty($subject)) ? "" : "Keyword: $subject\n";
  echo $description = (empty($description)) ? "" : "Description: $description\n";
  echo $publisher = (empty($publisher)) ? "" : "Publisher: $publisher\n"; 
  echo $date = (empty($date)) ? "" : "Date: $date\n";
  echo $type = (empty($type)) ? "" : "Type: $type\n";
  echo $format = (empty($format)) ? "" : "Format: $format\n";
  echo $identifier = (empty($identifier)) ? "" : "Identifier: $identifier\n";
  echo $source = (empty($source)) ? "" : "Source: $source\n"; 
  echo $language = (empty($language)) ? "" : "Language: $language\n"; 
  echo $relation = (empty($relation)) ? "" : "Relation: $relation\n\n";
  
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