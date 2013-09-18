<?php 
include 'LibBase.php';
include 'LibGw.php';
include 'LibHg.php';
include 'Factory.php';


$lib = Factory::createClass('LibHg');
if($lib->checkField("D1130581400", "581401")){

	var_dump($lib->getHistoryList());

}

?>