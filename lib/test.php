<?php 

include 'Factory.php';

header('Content-type:text/html; charset=utf-8');
$lib = Factory::createClass('LibJndx');
if($lib->checkField("2011050663", "050663")){

// 	var_dump($lib->renew('C2010604094'));
	var_dump($lib->getHistoryList());
	echo 1;
}else{
	echo 0;
}
// if($lib->checkField("20111003632", "yin543211")){

// 	var_dump($lib->getHistoryList());

// }


?>