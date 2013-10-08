<?php 
class LibJndx extends LibBase{
	

	public $responseHeader = '';
	public $pageContent = '';//保存返回的页面内容
	public $cookie = '';
	
	public $baseUrl = 'http://202.116.13.244/';
	
	public function __construct(){
	
	}
	
	public function checkField($studentNumber, $password, $formUrl='',$refer=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = 'http://libgp.jnu.edu.cn/ulogin.action';
		}
		if (empty($refer)){
			$refer = 'http://libgp.jnu.edu.cn/login.jsp?KeepThis=true&';
		}
		
		$field = array(
				'account'=>$studentNumber,
				'password'=>$password,
				'days'=> '3',
				'x'=>'37',
				'y'=>'8'
		);
		
		$param = '';
		foreach ($field as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($formUrl);
		$origin = 'http://'.$host;
		
		
		$header = array(
				'POST /ulogin.action HTTP/1.1',
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
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//跟随跳转
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		$this->parseResponseCookie($content);//从返回的内容中提取出cookie
		
		$redirectUrl = $info['url'];
		$pattern ='/account(.*)passwd/i';
		
		if(preg_match($pattern, $this->cookie,$match)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 针对暨南大学，登陆之后，获得登陆的code和名字
	 * @param string $code
	 * @param string $name
	 */
	public function checkCode($code, $name, $isLoanList = true){
		$formUrl = 'http://202.116.13.244/patroninfo*chx';
		$refer = 'http://libgp.jnu.edu.cn/users/goOpac.action';
	
		$field = array(
// 				'code'=>'0000155482',
// 				'name'=>'李宜桓',
				'code'=>$code,
				'name'=>$name,
		);
	
		$param = '';
		foreach ($field as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//跟随跳转
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/jndx_cookie.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$this->pageContent = $content;
		
		if (!$isLoanList){
			$historyUrl = "http://202.116.13.244/patroninfo~S1*chx/1064065/readinghistory";
			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL, $historyUrl);
			curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/jndx_cookie.txt');
			
			ob_start();
			curl_exec($ch2);
			$content = ob_get_contents();
			ob_end_clean();
			$info = curl_getinfo($ch2);
			curl_close($ch2);
			
			$this->pageContent = $content;
// 			var_dump($content);die;
			$this->responseHeader = $info;
		}
		
		
		
	}
	
	public function saveContent($requestUrl){
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $requestUrl);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch2, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/jndx_cookie.txt');
		
		ob_start();
		curl_exec($ch2);
		$content = ob_get_contents();
		ob_end_clean();
		
		$info = curl_getinfo($ch2);
		curl_close($ch2);
		$this->pageContent = $content;
		$this->responseHeader = $info;
		return $info;
	}
	
	/**
	 * 获得登录码
	 * @return multitype:unknown |NULL
	 */
	private function getCode(){
		$codeUrl = 'http://libgp.jnu.edu.cn/users/goOpac.action';
		$info = $this->saveContent($codeUrl);
		$content = $this->getContent();
		$pattern = '/<input(.*)name=\'code\'(.*)value=\'(\d*)\'>(.*)<input(.*)name=\'name\'(.*)value=\'(.*)\'>/i';
		if (preg_match($pattern, $content, $matches)){
			$code = $matches[3];
			$name = $matches[7];
			return array(
					'code'=>$code,
					'name'=>$name		
			);
		}else{
			return null;
		}
	}
	
	
	public function getHistoryList(){
		if (($user = $this->getCode()) == null){
			return null;
		}		
		
		$this->checkCode($user['code'], $user['name'], false);
		$pattern = $this->getHistoryListRegular();
		$content = $this->getContent();
		if (preg_match_all($pattern, $content, $matches)){
			$len = count($matches[1]);
			for ($i=0; $i<$len; $i++){
				$matches[1][$i] = $this->baseUrl.$matches[1][$i];
			}
			$result = array(
				'url'=>$matches[1],
				'title'=>$matches[2],
				'author'=>$matches[5],
			);
			return $result;
		}else{
			return null;
		}
	}
	
	public function getLoanList(){
		if (($user = $this->getCode()) == null){
			return null;
		}		
		
		$this->checkCode($user['code'], $user['name']);
		$pattern = $this->getLoanListRegular();
		$content = $this->getContent();
		if (preg_match_all($pattern, $content, $matches)){
// 			var_dump($matches);
			$len = count($matches[8]);
			for ($i=0; $i<$len; $i++){
				$matches[6][$i] = $matches[4][$i].'-'.$matches[6][$i];
				$matches[8][$i] = $this->baseUrl.$matches[8][$i];
				$matches[15][$i] = '20'.$matches[15][$i];
			}
			$result = array(
				'id'=>$matches[6],
				'url'=>$matches[10],
				'title'=>$matches[11],
				'author'=>$matches[13],
				'returnDate'=>$matches[17]
			);
			return $result;
		}else{
			return null;
		}
	}
	
	public function getContent(){
		return $this->pageContent;
	}
	
	public function getResponseHeader(){
		
	}
	
	public function renew($bookId){
		$renewUrl = $this->baseUrl.'opac.go?cmdACT=mylibrary.reloan&BARCODE='.$bookId.'&libcode=';
		$this->saveContent($renewUrl);
		$content = $this->getContent();
		$pattern = '/<\/html><script language=\"javascript\">(.|\n)*?alert\(\'(.*)\'\)/i';
		if (preg_match($pattern, $content, $match)){
			return $match[2];
		}else{
			return '服务器错误';
		}
		
	}
	
	/**
	 * 获取借书历史记录的正则表达式
	 * @return string
	 */
	private function getHistoryListRegular(){
		$regular = '';
		//获得第一个通配符 
		$regular .= '<td width=\"30\%\"><a href=\"\/(.*?)\">';//url
		$regular .= '<span class="patFuncTitleMain">(.*?)\ (.|\n)*?'; // 书名
		$regular .= '<td(.*)class=\"patFuncAuthor\">(.*?)\ (.*)<\/td>'; //作者
		return '/'.$regular.'/i';
	}
	
	private function getLoanListRegular(){
		$regular = '';
		$regular .= '<td(.*)class=\"patFuncMark\"><input(.*)type=\"checkbox\"(.*)name=\"(.*?)\"(.*)value=\"(\w*)\"(.*)';//id
		$regular .= '<td(.*)class=\"patFuncTitle\"(.*)<a href=\"\/(.*?)\">';//url
		$regular .= '<span class=\"patFuncTitleMain\">(.*?)\ (\w|\ |=)*\/\ (.*?)<\/span>(.|\n)*?';//title和author
		$regular .= '<td(.*)class=\"patFuncStatus\">(\ 到期\ )([\d|-]*)';//returnDate
		return '/'.$regular.'/i';
	}

	
	
}


?>