<?php
error_reporting(E_ALL);
include 'Gwtxz.class.php';
$user = new Gwtxz();

// $field = array(
// 		'username'=>$_POST['username'],
// 		'password'=>$_POST['password'],
// 		'login-form-type'=> 'pwd', //$_POST['login-form-type']
// 	);
$field = array(
		// 'userid'=>'20111003632',
		// 'userpwd'=>'yin543211',
		// 'imageField.x'=>'18',
		// 'imageField.y'=>'4',
		'username'=>'20111003443',
		'password'=>'wjw89757',
		'login-form-type'=> 'pwd', //$_POST['login-form-type']
	);


$formUrl = 'http://xg.gdufs.edu.cn/pkmslogin.form';//学工管理的登陆框
$requestUrl = $user->getRequestUrl($field['username'], 4);//Gwtxz类里内置的一些请求地址

//获取数字广外的通知
$requestUrl2 = "http://auth.gdufs.edu.cn/wps/myportal/001/00101/!ut/p/c5/fY1LDoIwFADPwgHMe_zLEj-RFhURVNoNqYnBSimEGNTb686dmcUsZjEg4IuRk2rkQ_VGaqhABHWWJ0jnxImynPlI03K7TEjoIgmBgwh_HcliiZSydOOtmItrhGpS1yeUUKFXF_eIbb4-zqOUvYuzmFUmaI-dzspuYEL7r0voH6S-ddLo2diefd7HCyUK0VgWZLaDngu7vZbKQPn3yoH_7YLGp9FxbDtAtAkMLZ8a0lsfBmXNWQ!!/";

//lib
$requestUrl3 = "http://lib.gdufs.edu.cn/bor.php";

$formUrl4 = "http://tsg.gdufs.edu.cn/pkmslogin.form";
$requestUrl4 = "http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp";
$formUrl5 = "http://auth.gdufs.edu.cn/pkmslogin.form";
$requestUrl5 = "http://auth.gdufs.edu.cn/wps/myportal/001/00101/!ut/p/c5";//数字广外首页

//数字广外的referUrl
$referUrl = 'http://auth.gdufs.edu.cn/wps/portal/newhome/!ut/p/c5/04_SB8K8xLLM9MSSzPy8xBz9CP0os3j_QA8DTycLI0t3Zw9TA09fD6MgDwtXQwN3U30_j_zcVP2CbEdFALkG2FQ!/dl3/d3/L2dBISEvZ0FBIS9nQSEh/';

$user->checkField($field, $formUrl4,"http://tsg.gdufs.edu.cn/");

// $user->checkField2("http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp","http://auth.gdufs.edu.cn/wps/myportal");
$result = $user->saveContent("http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp");

var_dump($result['redirect_url']);
die;
$content = $user->getContent();

// var_dump($user->cookie);
// $temp = $user->getAcademy(2);
// var_dump($temp);
// echo '<br/>';
// var_dump($user->firstContent);
// var_dump($content);
echo ($content);
// die;


