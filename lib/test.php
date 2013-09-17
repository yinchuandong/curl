<?php 
include 'LibBase.php';
include 'LibGw.php';
include 'LibHg.php';
include 'Factory.php';


$lib = Factory::createClass('LibGw');
$lib->getLoanList();



?>