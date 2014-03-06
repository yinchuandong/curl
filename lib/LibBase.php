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
	 * 续借图书
	 * @param string $bookId
	 * @return string 成功或者不成功的提示
	 */
	public abstract function renew($bookId);
	
	
	
	//===================公共函数====================================
	
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

?>