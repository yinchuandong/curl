<?php
error_reporting(E_ALL);
include './Gwtxz.class.php';
$user = new Gwtxz();

$field = array(
		// 'userid'=>'20111003632',
		// 'userpwd'=>'***',
		// 'imageField.x'=>'18',
		// 'imageField.y'=>'4',
		'username'=>'20111003632',
		'password'=>'***',
		'login-form-type'=> 'pwd', //$_POST['login-form-type']
	);


$requestUrl = 'http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp';
$formUrl = 'http://tsg.gdufs.edu.cn/pkmslogin.form';

$formUrl4 = "http://tsg.gdufs.edu.cn/pkmslogin.form";
$requestUrl4 = "http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp";

//数字广外的referUrl
$referUrl = 'http://auth.gdufs.edu.cn/wps/portal/newhome/!ut/p/c5/04_SB8K8xLLM9MSSzPy8xBz9CP0os3j_QA8DTycLI0t3Zw9TA09fD6MgDwtXQwN3U30_j_zcVP2CbEdFALkG2FQ!/dl3/d3/L2dBISEvZ0FBIS9nQSEh/';

$user->checkField($field, $formUrl4,"http://tsg.gdufs.edu.cn/");

$result = $user->saveContent("http://jw.gdufs.edu.cn/xf_xsyxxxk.aspx?xh=20111003632&xm=尹川东&gnmkdm=N121106");

// var_dump($result);
$content = $user->getContent();
$form = $user->getFormData();
$form['ddl_ywyl'] = "";
$form['ddl_kcgs'] = "";
$form['ddl_sksj'] = "";
$form['ddl_xqbs'] = 2;
// $form['kcmcGrid:_ctl4:xk'] = 'on';
// $form['kcmcGrid:_ctl9:xk'] = 'on';
$form['kcmcGrid:_ctl4:xk'] = 'on';
$form['kcmcGrid:_ctl3:xk'] = 'on';
$form['Button1'] = '提交';
var_dump($form);
// while(true){
// 	$user->doSelectCourse($form,"http://jw.gdufs.edu.cn/xf_xsyxxxk.aspx?xh=20111003632&xm=尹川东&gnmkdm=N121106");
// 	sleep(1);
// }
$user->doSelectCourse($form,"http://jw.gdufs.edu.cn/xf_xsyxxxk.aspx?xh=20111003632&xm=尹川东&gnmkdm=N121106");
// echo ($content);
echo 1;


