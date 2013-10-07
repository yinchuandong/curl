<?php 

include 'Factory.php';

header('Content-type:text/html; charset=utf-8');
$lib = Factory::createClass('LibHg');
if($lib->checkField("D1130580120", "801211")){

// 	var_dump($lib->renew('C2010604094'));
	var_dump($lib->getLoanList());

}
// if($lib->checkField("20111003632", "yin543211")){

// 	var_dump($lib->getHistoryList());

// }


?>