<?php 

include 'Factory.php';

header('Content-type:text/html; charset=utf-8');
$lib = Factory::createClass('LibJndx');
if($lib->checkField("2011050659", "050659")){
	var_dump($lib->renew('renew1-i3465229'));
	var_dump($lib->getLoanList());
	echo 1;
}else{
	echo 0;
}
die;
if($lib->checkField("2011050663", "050663")){
		var_dump($lib->renew('renew1-i3643976'));
		var_dump($lib->getLoanList());
	echo 1;
}else{
	echo 0;
}

// $lib = Factory::createClass('LibGw');
// if($lib->checkField("20111003632", "yin543211")){
// 	var_dump($lib->getHistoryList());echo 2;die;

// }else{
// 	echo 0;
// }



?>