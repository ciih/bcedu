<?php
function getScore() {
	$score = Array();
	for ($i=0; $i < 10; $i++) { 
		$score[$i]['name'] = $i;
		$score[$i]['scroe'] = $i;
	}
	return $score;
}

?>