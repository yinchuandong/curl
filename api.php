<?php
header("Content-type:text/html;charset=utf-8");
include 'Gwtxz.class.php';
$user = new Gwtxz();

$field = array(
		'username'=>$_REQUEST['studentNumber'],
		'password'=>$_REQUEST['password'],
		'login-form-type'=> 'pwd', //$_POST['login-form-type']
	);

$formUrl = 'http://auth.gdufs.edu.cn/pkmslogin.form';//学工管理的登陆框
$requestUrl = $user->getRequestUrl($field['username'], 4);//Gwtxz类里内置的一些请求地址

if($user->checkField($field, $formUrl)){
	echo '登陆成功';
}else{
	echo '登陆失败';
}