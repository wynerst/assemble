<?php

/**
** Assemble function for reading OAI output
** © Wardiyono, 2024 - wynerst@gmail.com
**/

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} else if (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

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
?>