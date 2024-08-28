<?php
/**
** Harvesting OAI output from OJS - just catch and send the text
** © Wardiyono, 2024 - wynerst@gmail.com

**/

// key to authenticate
define('INDEX_AUTH', '1');

require 'dbase.php';
require 'library.php';


if (isset($_GET["ojs"])) {
	$oai = rtrim($_GET["ojs"], '/\\');
	if (isset($_GET["resumptionToken"])) {
		$nextToken = $_GET["resumptionToken"];
		// https://domain.ojs/index.php/journal/oai?verb=ListRecords&resumptionToken=b021e917d1f9be0991d6216a45d724c0
		$url=$oai.'/oai?verb=ListRecords&resumptionToken='.$nextToken;
	} else {
		// https://domain.ojs/index.php/journal/oai?verb=ListRecords&metadataPrefix=oai_dc
		$url=$oai.'/oai?verb=ListRecords&metadataPrefix=oai_dc';
	}

echo '<img src=https://www.openarchives.org/images/OA100.gif height=50px></br>';
echo '<a href="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">Open new Journal URL</a></br>';
echo '<h4>Fetching articles from URL: ' . $url ."</h4>";
$recDel = 0;
$recAdd = 0;
$contents = file_get_contents($url);

$doc = new DOMDocument();
$doc->loadXML($contents);

$tokens=$doc->getElementsByTagName('resumptionToken');	
$records = $doc->getElementsByTagName('record');

foreach ($records as $record) {
  $deleted = $record->getElementsByTagName('header');
  if ($deleted[0]->getAttribute('status') == "deleted") {
	  $recDel = $recDel + 1;
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
  
  $arrdata = [
      "title"=>$title,
	  "creator"=>$creator,
	  "description"=>$description,
	  "subject"=>$subject,
	  "publisher"=>$publisher,
	  "date"=>$date,
	  "type"=>$type,
	  "format"=>$format,
	  "identifier"=>$identifier,
	  "source"=>$source,
	  "language"=>$language,
	  "relation"=>$relation
	  ];

  if (create($arrdata)) {
	  $recAdd++;
  } else {
      die("Error creating record");
  }

  // Process the extracted text content

  echo "<p>";
  //echo "Title: $title\n";
  echo $title = (empty($title)) ? "" : "<b>Title:</b> $title</br>";
  echo $creator = (empty($creator)) ? "" : "<b>Creator:</b> $creator</br>";
  echo $subject = (empty($subject)) ? "" : "<b>Keyword:</b> $subject</br>";
  echo $description = (empty($description)) ? "" : "<b>Description:</b> $description</br>";
  echo $publisher = (empty($publisher)) ? "" : "<b>Publisher:</b> $publisher</br>"; 
  echo $date = (empty($date)) ? "" : "<b>Date:</b> $date</br>";
  echo $type = (empty($type)) ? "" : "<b>Type:</b> $type</br>";
  echo $format = (empty($format)) ? "" : "<b>Format:</b> $format</br>";
  echo $identifier = (empty($identifier)) ? "" : "<b>Identifier:</b> $identifier</br>";
  echo $source = (empty($source)) ? "" : "<b>Source:</b> $source</br>"; 
  echo $language = (empty($language)) ? "" : "<b>Language:</b> $language</br>"; 
  echo $relation = (empty($relation)) ? "" : "<a href='$relation' target='blank'><b>Read this article</b></a>";
  echo "</p>";  
}
	echo "<p>";
	if ($recDel > 0) { echo "There are $recDel record(s) deleted.</br>"; }
	if ($recAdd > 0) { echo "Added $recAdd record(s) to the index.</br>"; }

if ($tokens->length > 0) {
	$resume=$tokens->item(0)->textContent;
	if ($resume <> "") {
		// 	echo 'Resumption token = <a href="https://ijdc.net/index.php/ijdc/oai?verb=ListRecords&resumptionToken='.$resume.'">Lanjut</a>';
		echo '<a href="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?ojs='.$oai.'&resumptionToken='.$resume.'">Load more data</a>';
	}
}

} else {
	
	echo '<img src=https://www.openarchives.org/images/OA100.gif height=50px></br>';
	echo '<form method="GET">';
	echo 'Retrieve Open Access Jurnal articles form this OJS URL: <input type="text" name="ojs">&nbsp;&nbsp;';
	echo '<input type="submit">';
	echo '</form>';

	$stat = StatData();
	$i=0;

	echo "<h5>Available data</h5>";
	if (is_array($stat)) {
		echo '<table style="font-size:9pt; border: 1px solid black; border-collapse: collapse;">';
		echo '<th>No.</th><th>Title</th><th>URL</th><th>Article(s)</th>';
		foreach($stat as $key => $value) {
			$i= ++$i;
			echo "<tr><td style='border: 1px solid black;'>$i</td>";
			foreach ($value as $tag => $content) {
				if ($tag == "Title") {
					echo "<td style='border: 1px solid black;'><a href='list.php?&search=$content'>$content</a></td>";
				} else {
					echo "<td style='border: 1px solid black;'>$content</td>";
				}
			}
			echo "</tr>";
		}
		echo "</table><br/>";
		echo "<a href='list.php'>Browse harvested data</a>";
	} else {
		echo "&nbsp;";
	}
}

?>