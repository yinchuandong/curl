<?php 

include 'lib/factory.class.php';

header('Content-type:text/html; charset=utf-8');

//=============暨大=====================
// $lib = Factory::createClass('LibJndx');

// if($lib->checkField("2011050666", "050666")){
// 	var_dump($lib->renew('renew0-i2011719'));
// 	// 	var_dump($lib->getLoanList());
// 	echo 1;
// }else{
// 	echo 0;
// }
// die;

// if($lib->checkField("2011050659", "050659")){
// 	var_dump($lib->renew('renew1-i3465229'));
// // 	var_dump($lib->getLoanList());
// 	echo 1;
// }else{
// 	echo 0;
// }
// die;
// if($lib->checkField("2011050663", "050663")){
// 		var_dump($lib->renew('renew1-i3643976'));
// 		var_dump($lib->getLoanList());
// 	echo 1;
// }else{
// 	echo 0;
// }

// =======广外=====================
$lib = Factory::createClass('LibGw');
if(($userInfo = $lib->checkField("***", "***")) != false){
// 	$responseHeader = $lib->saveContent("http://lib.gdufs.edu.cn/uindex.php");
// 	var_dump($lib->getContent());
// 	$content = $lib->getContent();
// 	file_put_contents("./testlib_content.txt", $content);
// 	var_dump($userInfo);
// 	var_dump($lib->getLoanList());
// 	var_dump($lib->getHistoryList());echo 2;die;

}else{
	echo 0;
}

//=========华工=========
// $lib = Factory::createClass('LibHg');
// if(($userinfo = $lib->checkField("D1130580120", "801211")) != false){
// 	var_dump($lib->getHistoryList());
// // 	var_dump($userinfo);
	
// }else{
// 	echo 0;
// }

?>