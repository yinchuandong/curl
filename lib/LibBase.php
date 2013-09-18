<?php 
abstract class LibBase {
	/**
	 * 检查用户名密码是否正确
	 * @param String $studentNumber
	 * @param String $password
	 * @param String $formUrl
	 * @param String $refer
	 * @return bool
	 */
	public abstract function checkField($studentNumber, $password, $formUrl='',$refer='');	
	
	/**
	 * 保存页面内容
	 * @param $requestUrl
	 * @return string
	 */
	public abstract function saveContent($requestUrl);
	
	/**
	 * 获得借阅列表
	 */
	public abstract function getLoanList();
	
	/**
	 * 获得历史列表
	 */
	public abstract function getHistoryList();
	
	/**
	 * 得到页面返回的内容
	 * @return string
	 */
	public abstract function getContent();
	
	/**
	 * 得到responseHeader信息
	 * @return array
	 */
	public abstract function getResponseHeader();
	
	
	
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
	
	
	
	
	
	
	
	
	
}

?>