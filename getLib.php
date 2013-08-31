<?php
// error_reporting(E_ALL);
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


// $user->checkField($field, $referUrl2);
// $user->saveContent($requestUrl3);
// $content = $user->getContent();

// // $temp = $user->getAcademy(1);
// // var_dump($temp);
// // echo '<br/>';
// $newurl = $user->getRedirectToLibUrl();
// $user->saveContent($newurl);
// $content = $user->getContent();



// var_dump($content);
$content = '<div class="tabcontent" id="history" style="display:none;"> 
<br> 
<span class="tr1">点击可查看更多信息，续借，删除，等等</span> 
 
<table class="indent1" border="0" cellspacing="2" width="80%"> 
<!-- 
 <tr> 
    <td class=td1>GWD50</td> 
 </tr> 
--> 
 <tbody><tr> 
  <td class="td2" align="left" width="35%"> 
         外借 
  </td> 
  <td class="td1"><a href="javascript:replacePage(\'http://opac.gdufs.edu.cn:8991/F/FRQHH8TPNTSVUIJ7UR3YC36ESE7GQ7AIL3K238YJ1FEJ4TN5FS-33341?func=bor-loan&adm_library=GWD50\');">3    </a></td> 
 </tr> 
 <tr> 
  <td class="td2" align="left"> 
        借阅历史列表 
  </td> 
    <td class="td1"><a href="javascript:replacePage(\'http://opac.gdufs.edu.cn:8991/F/FRQHH8TPNTSVUIJ7UR3YC36ESE7GQ7AIL3K238YJ1FEJ4TN5FS-33342?func=bor-history-loan&adm_library=GWD50\');">20   </a></td> 

 </tr> 
 
</div>';
$user->parseLibContent($content);

