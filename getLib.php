<?php
// error_reporting(E_ALL);
 header ("Cache-Control: no-cache, must-revalidate"); 
include 'Library.php';
$user = new Library();

// $field = array(
// 		'username'=>$_POST['username'],
// 		'password'=>$_POST['password'],
// 		'login-form-type'=> 'pwd', //$_POST['login-form-type']
// 	);
$field = array(
		'userid'=>'20111003632',
		'userpwd'=>'yin543211',
		'imageField.x'=>'18',
		'imageField.y'=>'4',
		// 'username'=>'20111003632',
		// 'password'=>'yin543211',
		// 'login-form-type'=> 'pwd',
	);

//lib
$requestUrl3 = "http://lib.gdufs.edu.cn/uindex.php";



//数字广外的referUrl
$referUrl = 'http://auth.gdufs.edu.cn/wps/portal/newhome/!ut/p/c5/04_SB8K8xLLM9MSSzPy8xBz9CP0os3j_QA8DTycLI0t3Zw9TA09fD6MgDwtXQwN3U30_j_zcVP2CbEdFALkG2FQ!/dl3/d3/L2dBISEvZ0FBIS9nQSEh/';

//
$referUrl2 = 'http://lib.gdufs.edu.cn/bor.php';


$user->checkField($field, $referUrl2);
$user->saveContent($requestUrl3);
$content = $user->getContent();


$newurl = $user->getRedirectToLibUrl();
$user->saveContent($newurl);
$content = $user->getContent();

$uriList = $user->parseLibContent($content);

$user->saveContent($uriList['url'][0]);
$content = $user->getContent();

$content = $user->getLoanList($content);
var_dump($content);

// $test = '<td class=td1 valign=top width="2%" align="center"><input type="checkbox" name="c000320687000040">14124</td>';
// // echo $test;
// echo strip_tags($test);












