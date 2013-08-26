<?php
include 'Gwtxz.class.php';

$user = new Gwtxz();

$field = array(
		'username'=>$_POST['username'],
		'password'=>$_POST['password'],
		'login-form-type'=>$_POST['login-form-type'],
	);
////	var_dump($field);
$formUrl = 'http://xg.gdufs.edu.cn/pkmslogin.form';
//$referUrl = "http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/SSGLZXT/SSAP/V_SS_SSXXST&tfile=XSCKMB/BDTAG&filter=V_SS_SSXXST:XH='".$field['username']."'";
$referUrl = $user->getReferUrl($field['username'], 2);
$user->checkField($field, $formUrl);
$user->saveContent($referUrl);
$content = $user->getContent();

//$content = $user->getStudentNumber(2);
var_dump($content);


