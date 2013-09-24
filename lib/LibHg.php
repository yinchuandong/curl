<?php 
class LibHg extends LibBase{
	

	public $responseHeader = '';
	public $pageContent = '';//保存返回的页面内容
	public $cookie = '';
	
	public $baseUrl = 'http://202.38.232.10/opac/servlet/';
	
	public function __construct(){
	
	}
	
	public function checkField($studentNumber, $password, $formUrl='',$refer=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = $this->baseUrl.'opac.go';
		}
		if (empty($refer)){
			$refer = $this->baseUrl."mylib.go?cmdACT=mylibrary.index&method=myinfo";
		}
		
		$field = array(
				'userid'=>$studentNumber,
				'passwd'=>$password,
				'cmdACT'=> 'mylibrary.login',
				'libcode'=>'',
				'method'=>'mylib'
		);
		
		$param = '';
		foreach ($field as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($formUrl);
		$origin = 'http://'.$host;
		
		
		$header = array(
				'POST /opac/servlet/opac.go HTTP/1.1',
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
		$pattern ='#\=reader\.info#';
		if(preg_match($pattern, $content)){
			return true;
		}else{
			return false;
		}
	}
	
	public function saveContent($requestUrl){
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $requestUrl);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch2, CURLOPT_COOKIE, $this->cookie);
		
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
	
	
	public function getHistoryList(){
		$historyUrl = $this->baseUrl.'opac.go?cmdACT=bookshelf.list';
		$this->saveContent($historyUrl);
		$pattern = $this->getHistoryListRegular();
		$content = $this->getContent();
		
		if (preg_match_all($pattern, $content, $matches)){
			$len = count($matches[4]);
			for ($i=0; $i<$len; $i++){
				$matches[4][$i] = $this->baseUrl.$matches[4][$i];
			}
			$result = array(
				'order'=>$matches[1],
				'url'=>$matches[4],
				'title'=>$matches[5],
				'author'=>$matches[6]
			);
			return $result;
		}else{
			return null;
		}
	}
	
	public function getLoanList(){
		echo 'hg-->getLoanList';
	}
	
	public function getContent(){
		return $this->pageContent;
	}
	
	public function getResponseHeader(){
		
	}
	
	public function renew($bookId){
		
	}
	
/**
	 * 获取借书历史记录的正则表达式
	 * @return string
	 */
	private function getHistoryListRegular(){
		$regular = '';
		//获得第一个通配符 
		$regular .= '<td>(.)*?<\/td>';
		$regular .= '<td>(.|\n)*?<a(.*?)href=\"\.\.\/servlet\/(.*)\">(.*)<\/a><\/td>'; // url, 书名
		$regular .= '<td>(.*)<\/td>'; //作者
		$regular .= '<td>(.*)<\/td>'; //出版社
		$regular .= '<td>(.*)<\/td>'; //isbn
		$regular .= '<td>(.*)<\/td>'; //分类号
		$regular .= '<td>(.*)<\/td>(.|\n)*?'; //文献类型
		return '/'.$regular.'/i';
	}
	

	
	
}


?>