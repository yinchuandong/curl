<?php
class Library {
	public $responseInfo = '';
	public $pageContent = '';//保存返回的页面内容

	public $cookie = '';
	
	
	public function __construct(){
		
	}
	
	/**
	 * 从返回的内容中提取出cookie
	 * @param String $responseHeader
	 */
	public function parseResponseCookie($responseHeader){
		list($header, $body) = explode("\r\n\r\n", $responseHeader);
		preg_match_all("/set\-cookie:([^\r\n]*)/is", $header, $matches);
		foreach ($matches[1] as $value) {
			$this->cookie .= $value.'; ';
		}
		// var_dump($$re);
		// echo $this->cookie;
		
	}
	
	/**
	 * 发送表单数据，并存储cookie
	 * @param array $field //表单的数据
	 * @param string $formUrl //广外统一验证入口 
	 * @param string $refer //页面的refer,由于数字广外采用了xscf，如果要获取数字广外的内容，必须指定该项
	 * 学工管理： http://xg.gdufs.edu.cn/pkmslogin.form 
	 * 正方教务： http://jw.gdufs.edu.cn/pkmslogin.form
	 * 
	 */
	function checkField($field, $formUrl='',$refer=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = 'http://jw.gdufs.edu.cn/pkmslogin.form';
		}
		if (empty($refer)){
			$refer = $this->getReferUrl();
		}
		
		$param = '';
		foreach ($field as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($formUrl);
		$origin = 'http://'.$host;
// 		echo $host.'===='.$origin.'===='.$refer;
		
		
		$header = array(
			'POST /pkmslogin.form HTTP/1.1',
			'Host: '.$host,
			'Connection: keep-alive',
			'Content-Length: '.strlen($param),
			'Cache-Control: max-age=0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Origin: '.$origin,
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
			'Content-Type: application/x-www-form-urlencoded',
			'Referer: '.$refer,
			'Accept-Encoding: gzip,deflate,sdch',
			'Accept-Language: zh-CN,zh;q=0.8',
			'Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3',
			
		);
		
// 		var_dump($header);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		// curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
		// $this->responseInfo = curl_getinfo($ch);
		curl_close($ch);
		
		$this->parseResponseCookie($content);//从返回的内容中提取出cookie
		
		$pattern ='#<TITLE>Success<\/TITLE>#';
		if(preg_match($pattern, $content)){
//			echo 1;
			return true;
		}else{
//			echo 2;
			return false;
		}
	
	}
	
	/**
	 * 保存页面返回内容
	 * @param string $requesUrl 请求地址的url
	 * 学工管理：http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/XSJBXX/T_XSJBXX_XSJBB&tfile=XGMRMB/detail_BDTAG
	 * 正方教务: http://jw.gdufs.edu.cn/xskbcx.aspx?xh=20111003632
	 */
	function saveContent($requesUrl){
//		$this->checkField($field);
		
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $requesUrl);
		curl_setopt($ch2, CURLOPT_COOKIE, $this->cookie);
		// curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
	
		ob_start();
		curl_exec($ch2);
		$content = ob_get_contents();
		ob_end_clean();
		
		$this->pageContent = $content;

		curl_close($ch2);	
	}
	
	
	public function getContent(){
		return $this->pageContent;
	}
	
	/**
	 * 从一串url地址中，解析主机
	 * 
	 * @param string $url 如：http://www.php.net/pub/
	 * @return string $host 如：www.php.net
	 */
	public function parseHost($url){
		$search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
		$url = trim($url);
		preg_match($search, $url ,$match);
		return $match[4];
	}
	
	/**
	 * 生成请求的地址
	 * @param string $schoolNumber 学号
	 * @param int $type 类型： 1为正方管理系统>>我的信息；2为正方管理系统>>我的课表；3为学工管理>>我的基本信息；4为学工管理>>我的宿舍信息
	 * @return string $requstUrl 返回请求的地址
	 */
	public function getRequestUrl($schoolNumber, $type){
		$requestUrl = '';
		switch ($type){
			case 1:
				$requestUrl = 'http://jw.gdufs.edu.cn/xsgrxx.aspx?xh='.$schoolNumber;
				break;
			case 2:
				$requestUrl = 'http://jw.gdufs.edu.cn/xskbcx.aspx?xh='.$schoolNumber;
				break;
			case 3:
				$requestUrl = 'http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/XSJBXX/T_XSJBXX_XSJBB&tfile=XGMRMB/detail_BDTAG';
				break;
			case 4:
				$requestUrl = "http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/SSGLZXT/SSAP/V_SS_SSXXST&tfile=XSCKMB/BDTAG&filter=V_SS_SSXXST:XH='".$schoolNumber."'";
				break;
			default:
				$requestUrl = 'http://jw.gdufs.edu.cn/xsgrxx.aspx?xh='.$schoolNumber;
				
		}
		return $requestUrl;		
	}
	
	/**
	 * 当获取数字广外的内容时，才需要用到
	 * @return string $referUrl 
	 */
	public function getReferUrl(){
		$referUrl = "http://auth.gdufs.edu.cn/wps/myportal/001/00101/!ut/p/c5/fY1LDoIwFADPwgHMe_zLEj-RFhURVNoNqYnBSimEGNTb686dmcUsZjEg4IuRk2rkQ_VGaqhABHWWJ0jnxImynPlI03K7TEjoIgmBgwh_HcliiZSydOOtmItrhGpS1yeUUKFXF_eIbb4-zqOUvYuzmFUmaI-dzspuYEL7r0voH6S-ddLo2diefd7HCyUK0VgWZLaDngu7vZbKQPn3yoH_7YLGp9FxbDtAtAkMLZ8a0lsfBmXNWQ!!/";
		return $referUrl;
	}
	
	public function getRedirectToLibUrl(){
		$pattern = '#<a href=\"(.*)\" target=\"s\">(.*)<\/a>#';
		
		$content = $this->getContent();
		if (preg_match($pattern, $content, $match)) {
			return $match[1];
		}else{
			return null;
		}
	}

	public function parseLibContent($text){
		
		// $pattern = '#<div class="tabcontent" id="history" display="none;">(.*)<\/div>#';
		$pattern = '/div(.*)id="history"(.*)>(.*)<\/div>/ism';
		// $pattern = '/<DIV[^>/]*?\s+class=\"tabcontent\" id=\"history\" display="\none;\"[^>/]*>(.*?)<\/DIV>/i';
		if (preg_match($pattern, $text, $match)) {
			var_dump($match);
		}else{
			var_dump('12312'.$text);
		}
	}

	
	
	
	
	
	
	
	
	
	
	
	
}