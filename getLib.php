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


// $user->checkField($field, $referUrl2);// 检查用户名或密码是否正确，并且保存了cookie

// $uriList = $user->getFinalUrl($requestUrl3);//得到跳转至具体页面的url，具体看函数注释
// // $content = $user->getRenewUrl($uriList['url'][0]);
// // var_dump($content);
$user->checkField("20111003632", "yin543211");
$uriList = $user->getFinalUrl();//得到跳转至具体页面的url，具体看函数注释
// die;
$content = $user->getLoanList($uriList['url'][0]); //当前借阅列表
// $content = $user->getHistoryList($uriList['url'][1]); //借阅历史
var_dump($content);



// $data = $user->saveContent("http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp");
// $user->saveContent($data['redirect_url']);
// $content = $user->getContent();

// $content = $user->getRenewUrl($uriList['url'][0]);
// var_dump($uriList);












