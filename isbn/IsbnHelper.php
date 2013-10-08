<?php 
class IsbnHelper {
	
	private $pageContent = '';
	public $baseUrl = 'http://202.38.232.10/opac/servlet/';
	
	public function getBookInfo($title){
		
		$formUrl = $this->baseUrl.'opac.go';
		$refer = $this->baseUrl."mylib.go?cmdACT=mylibrary.index&method=myinfo";
		
		$field = array(
				'cmdACT'=>'simple.list',
				'TABLE'=>'',
				'RDID'=>'ANONYMOUS',
				'CODE'=>'',
				'SCODE'=>'',
				'PAGE'=>'',
				'CLANLINK'=>'',
				'libcode'=>'',
				'MARCTYPE'=>'',
				'ORGLIB'=>'SCUT',
				'FIELD1'=>'TITLE',
				'VAL1'=>$title,
				'MODE'=>'FRONT'
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
		$pattern = $this->getIsbnRegular();
		if(preg_match($pattern, $content, $matches)){
			$result = array(
					'isbn'=>$matches[10],
					'callNumber'=>$matches[14]
					
			);
			return $result;
		}else{
			return null;
		}
		
		
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
	
	
	private function getIsbnRegular(){
		$pattern = '';
		$pattern .= '<TD bgcolor=\"\#FFFFFF\">(\d)*<\/TD>';
		$pattern .= '<TD(.*)><a(.*)>(.*)<\/a><\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern .= '<TD(.*)>(.*)<\/TD>';
		$pattern = '/'.$pattern.'/i';
		return $pattern;
	}
	
	
	
	
}



?>