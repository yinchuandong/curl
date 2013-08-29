<?php
	$url = "http://jw.gdufs.edu.cn/pkmslogin.form";
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
		'Host: '.'jw.gdufs.edu.cn',
		'Connection: keep-alive',
		'Content-Length: '.strlen($param),
		'Cache-Control: max-age=0',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Origin: '.$url,
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
		'Content-Type: application/x-www-form-urlencoded',
		'Referer: '.$url,
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
	// $request_header = $meta['request_header'];
	// var_dump($request_header);
	
	// var_dump($content);
	echo '<br/><br/>';
	// list($header, $body) = explode("\r\n\r\n", $content);  
	// // 解析COOKIE  
	// preg_match("/Set\-Cookie:([^\r\n]*)/i", $content, $matches); //这个地方需要变通一下。cookie不是只有1个。  
	// // 后面用CURL提交的时候可以直接使用  
	// // curl_setopt($ch, CURLOPT_COOKIE, $cookie);  
	// $cookie = $matches[1];  
	  
	// //修改如下：  
	  
	// list($header, $body) = explode("\r\n\r\n", $temp);  
	// // 解析COOKIE  
	preg_match_all("/set\-cookie:([^\r\n]*)/is", $content, $matches);  
	//将cookie以字符串形式发送cookie  
	// $cookie_str = join('; ',$matches[1]);
	var_dump($matches);
?>