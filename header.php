<?php
header("Content-type:text/html;charset=utf-8");
init();
function init(){

	$url = "http://auth.gdufs.edu.cn/pkmslogin.form";
	$field = array(
		'username'=>'20111003632',
		'password'=>'yin543211',
		'login-form-type'=>'pwd',
	);

	$param = '';
	foreach ($field as $key => $value){
		$param .= "$key=".urlencode($value)."&";
	}
	$param = substr($param, 0,-1);
	
	$header = array(
		'POST /pkmslogin.form HTTP/1.1',
		'Host: '.'auth.gdufs.edu.cn',
		'Connection: keep-alive',
		'Content-Length: '.strlen($param),
		'Cache-Control: max-age=0',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Origin: http://auth.gdufs.edu.cn',
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
		'Content-Type: application/x-www-form-urlencoded',
		'Referer: http://auth.gdufs.edu.cn/wps/portal/newhome/!ut/p/c5/04_SB8K8xLLM9MSSzPy8xBz9CP0os3j_QA8DTycLI0t3Zw9TA09fD6MgDwtXQwN3U30_j_zcVP2CbEdFALkG2FQ!/dl3/d3/L2dBISEvZ0FBIS9nQSEh/',
		'Accept-Encoding: gzip,deflate,sdch',
		'Accept-Language: zh-CN,zh;q=0.8',
		'Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3',
		// 'Set-Cookie: 14'
		
	);
		

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

	// curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$content = curl_exec($ch);
	// $meta = curl_getinfo($ch);
	curl_close($ch);
	
	list($header, $body) = explode("\r\n\r\n", $content);
	
	preg_match_all("/set\-cookie:([^\r\n]*)/is", $header, $matches);  
	//将cookie以字符串形式发送cookie  
	// $cookie_str = join('; ',$matches[1]);
	// $session_id = $matches[1][0];
	// $pd_id = $matches[1][1];
	// $cookie = $session_id.'; '.$pd_id;
	// // echo $session_id.' '.$pd_id;
	// $cookie;

	foreach ($matches[1] as $value) {
		$cookie = $value.'; ';
	}
	foreach ($matches[0] as $value) {
		array_push($header, $value);
	}
	// array_push($header, $matches[0][0]);
	// array_push($header, $matches[0][1]);

	// $url2 = "http://auth.gdufs.edu.cn/wps/myportal/001/00101";
	$url2 = "http://auth.gdufs.edu.cn/wps/myportal/001/00101/!ut/p/c5/fY1LDoIwFADPwgHMe_zLEj-RFhURVNoNqYnBSimEGNTb686dmcUsZjEg4IuRk2rkQ_VGaqhABHWWJ0jnxImynPlI03K7TEjoIgmBgwh_HcliiZSydOOtmItrhGpS1yeUUKFXF_eIbb4-zqOUvYuzmFUmaI-dzspuYEL7r0voH6S-ddLo2diefd7HCyUK0VgWZLaDngu7vZbKQPn3yoH_7YLGp9FxbDtAtAkMLZ8a0lsfBmXNWQ!!/";
	// echo $cookie;
	$ch2 = curl_init($url2);
	// curl_setopt($ch, CURLOPT_HEADER, true);
	// curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch2, CURLOPT_COOKIE, $cookie);
	// curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

	// curl_setopt($ch, CURLOPT_NOBODY, true);
	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$content = curl_exec($ch2);
	// $meta = curl_getinfo($ch);
	curl_close($ch2);

	var_dump($content);die();
}



?>