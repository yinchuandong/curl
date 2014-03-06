<?php

$formurl = 'http://202.116.196.4/ees/login.php';
$qUrl = 'http://202.116.196.4/ees/student/studentmain.php';

$data = array(
	'userid'=>'20111003632',
	'userpswd'=>'888888',
	'usertype'=>'0'		
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $formurl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

// 抓取URL并把它传递给浏览器
curl_exec($ch);
curl_close($ch);

$header = array(
		'GET /ees/student/ajax_getProblem.php?qid=113&type=1 HTTP/1.1',
		'Connection: keep-alive',
		'Cache-Control: max-age=0',
		'Accept: */*',
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.48 Safari/537.36',
		'Content-Type: application/x-www-form-urlencoded',
		'Referer: http://202.116.196.4/ees/student/pushQuestion.php?unitStr=0&type=1',
		'Accept-Encoding: gzip,deflate,sdch',
		'Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,es;q=0.4,zh-TW;q=0.2'
			
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $qUrl);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

// 抓取URL并把它传递给浏览器
$content = curl_exec($ch);
$response = curl_getinfo($ch);
curl_close($ch);
file_put_contents("test2.txt", $content);
var_dump($response);
var_dump($content);













