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
	
	
	//=====步骤2,获得跳转到具体当前借书或者历史列表的rul======================
	public function getRedirectToLibUrl(){
		$pattern = '#<a href=\"(.*)\" target=\"s\">(.*)<\/a>#';
		
		$content = $this->getContent();
		if (preg_match($pattern, $content, $match)) {
			return $match[1];
		}else{
			return null;
		}
	}

	/**
	 * 解析从上一步获取的图书管界面的内容，获得跳转的url和对应的数量,返回一个二位数组
	 * url[0]外借的url, num[0]外接的数量
	 * url[1]借阅历史的url, num[1]借阅的历史数量
	 * url[2]预约请求的url,num[2]预约请求的数量
	 * url[2]预订请求的url,num[2]预定请求的数量
	 * url[2]现金事物的url,num[2]现金事物的数量
	 * 
	 * @param string $text
	 * @return array $result = array('url'=>array(),'num'=>array());
	 */
	public function parseLibContent($text){
		
		// $pattern = '#<div class="tabcontent" id="history" display="none;">(.*)<\/div>#';
		// $pattern = '/div(.*)id="history"(.*)>(.*)<\/div>/ism';
		
		//$pattern = '(<!--[\w\W\r\n]*?-->)|(<script(.*)>(.|\n)*?<\/script>)';
		
		$noNote = $this->escapeNote($text);
			
		$noScript = $this->escapeScript($noNote);
		// var_dump($noScript);
	
		$pattern = '#<div(.*)id="?history"?(.*)>(.|\n)*</div>#';
		$pattern2 = '/<td class="?td1"?>(.*)<\/td>/ism';
		$pattern3 = '/<a href="javascript:replacePage\(\'(.*)\'\);">(.*)<\/a>/i';
		if (preg_match($pattern, $noScript, $match)) {
			// var_dump($match[0]);
			$text2 = $match[0];
		}else{
			var_dump('false1'.$text);die;
		}

		if (preg_match($pattern2, $text2, $match2)) {
			// var_dump($match2);
			$text3 = $match2[0];
		}else{
			echo 'false2;'; die;
		}

		if (preg_match_all($pattern3, $text3, $match3)) {
// 			var_dump($match3[2]);
			$result = array(
				'url' => $match3[1],
				'num' => $match3[2]
			);
			return $result;
		}else{
			echo 'false3;';die;
		}
		
	}
	
	
	public function getLoanList($content){
		$content = $this->escapeNote($content);
// 		$content = $this->escapeScript($content);
// 		echo $content;
		$pattern = $this->getLoanListRegular();
// 		$pattern = '/<td class=td1 id=centered(.*)>(.)*/i';
		if(preg_match_all($pattern, $content, $matches)){
			$result['id'] = $matches[5];
			$result['author'] = $matches[8];
			$result['url'] = $matches[11];
			$result['title'] = $matches[12];
			$result['publishYear'] = $matches[15];
			$result['returnTime'] = $matches[18];
			$result['payment'] = $matches[21];//这里有点问题，因为我没有欠钱，所以看不到
			$result['location'] = $matches[24];
			$result['searchNumber'] = $matches[27];
			return $result;
		}else{
			echo 'getfalse';
		}
		
	}
	
	/**
	 * 获得借书列表的正则表达式;
	 */
	public function getLoanListRegular(){
		$regular = '';
		//获得第一个通配符
		$regular .= '<td class=td1 id=centered(.*)>(.)*<\/td>(.|\n)*?';
		//checkbok的name里面的值
		$regular .= '<td class=td1(.)*><input type="checkbox" name="(.*)"><\/td>(.|\n)*?';
		//作者
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//书名
		$regular .= '<td class=td1(.)*><a href="(.*)" target=_blank>(.*)<\/a><\/td>(.|\n)*?';
		//出版日期
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//应还日期 
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//罚款
		$regular .= '<td class=td1(.)*>(.)*<\/td>(.|\n)*?';
		//南校还是北校
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//索书号
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		
		return '/'.$regular.'/i';
	}
	
	//=======================================================
	
	/**
	 * 去除html注释
	 * @param string $text
	 */
	public function escapeNote($text){
		return preg_replace('/<!--[\w\W\r\n]*?-->/i', "", $text);
	}
	
	/**
	 * 去除script标签
	 * @param string $text
	 * @return string
	 */
	public function escapeScript($text){
		return preg_replace('/<script(.*)>(.|\n)*?<\/script>/i', "", $text);
	}
	
	
	
	
	
	
	
}