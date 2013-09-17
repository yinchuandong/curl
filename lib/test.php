<?php 
include 'LibBase.php';
include 'LibGw.php';
include 'LibHg.php';
include 'Factory.php';



function test(LibBase $gw){
	$lib = Factory::createClass('LibGw');
	$gw = $lib->getLoanList();
	$gw->getHistoryList();
}

$lib = Factory::createClass('LibGw');
$lib->getHistoryList();



?>