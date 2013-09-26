<?php 

include 'Factory.php';


$lib = Factory::createClass('LibGw');
if($lib->checkField("20111003632", "yin543211")){

	var_dump($lib->getHistoryList());

}
// if($lib->checkField("20111003632", "yin543211")){

// 	var_dump($lib->getHistoryList());

// }
?>