<?php 
// include 'LibBase.php';
// include 'LibGw.php';
// include 'LibHg.php';
include 'Factory.php';


$lib = Factory::createClass('LibGW');
if($lib->checkField("20111003632", "yin543211")){

	var_dump($lib->renew('c000176609000020'));

}

?>