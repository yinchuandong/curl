<?php
error_reporting(E_ALL);
include 'Gwtxz.class.php';
$user = new Gwtxz();

$field = array(
		
		'username'=>'',
		'password'=>'',
		'login-form-type'=> 'pwd', //$_POST['login-form-type']
	);


$formUrl = 'http://xg.gdufs.edu.cn/pkmslogin.form';//学工管理的登陆框
$requestUrl = $user->getRequestUrl($field['username'], 4);//Gwtxz类里内置的一些请求地址

$user->checkField($field, $formUrl);
$user->saveContent($requestUrl);

$academy = $user->getAcademy(2);
$username = $username->getUsername(2);

var_dump($academy);



