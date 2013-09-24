<?php 
include 'Factory.php';


$lib = Factory::createClass('LibHg');
if($lib->checkField("D1130581400", "581401")){

	var_dump($lib->getHistoryList());

}
// if($lib->checkField("20111003632", "yin543211")){

// 	var_dump($lib->getHistoryList());

// }
?>