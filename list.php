<?php
/**
** Harvesting OAI output from OJS - just catch and send the text
** © Wardiyono, 2024 - wynerst@gmail.com


$newData = array('field1' => 'value1', 'field2' => 'value2');
if (create($newData)) {
    echo "Record created successfully";
} else {
    echo "Error creating record";
}

**/

// key to authenticate
define('INDEX_AUTH', '1');

require 'dbase.php';
require 'library.php';

$strTxt = "";

echo '<img src=https://www.openarchives.org/images/OA100.gif height=50px></br></br>';
echo "<form method='POST' action='list.php'>Search: <input type='text' name='search'>";
echo "<input type='submit'></form>";
echo '<h4>Fetching articles from database:</h4>';


if (isset($_POST["search"]) OR isset($_GET["search"])) {
	if (isset($_POST["search"]) AND $_POST["search"] <> "") {
		$strTxt = $_POST["search"];
	} elseif ($_GET["search"] <> "") {
		$strTxt = $_GET["search"];
	} else {
		$strTxt = "";
	}
}

$lst=read($strTxt);

if (is_array($lst)) {
//	echo $strTxt.'<br/>';
//	echo $lst['sql'].'<br/>';
	echo $lst['rows']." record(s) found for $strTxt.<br/>";
	echo "<table width=100%>";

		foreach($lst as $key => $value) {
			if (is_array($value) AND ($key <> 'sql' OR $key <> 'rows') ) {
				echo "<tr><td>";
				echo "<table width=100%>";
				$key = $key + 1;
				echo "<tr><td colspan=2 bgcolor='lite-grey'>".$key."</td></tr>";
				foreach ($value as $tag => $content) {
					echo "<tr>";
					$content = str_ireplace($strTxt, "<fonts style='background-color:Yellow';>$strTxt</fonts>", $content);
					echo "<td valign='top' bgcolor='grey'>$tag</td><td>$content";
					if ($tag=="relation") {
						echo "&nbsp;<a href=".$content.">Open this article</a>";
					}
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
		}
		echo "</td></tr>";

	echo "</table>";
} else {
	echo "<h4>No-Data</h4>";
}

?>