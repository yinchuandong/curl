<?php 
abstract class LibBase {
	
	/**
	 * 保存从页面解析的cookie
	 */
	protected $cookie = '';
	
	/**
	 * 文件名：保存通过curl自带的解析cookie，可用可不用
	 * */
	protected $cookieFile = '';
	
	/**
	 * 检查用户名密码是否正确 
	 * 如果失败，返回false；
	 * 如果成功，返回user数组：{username//用户名, academy//学院,major//专业, grade//毕业年份}
	 * @param String $studentNumber
	 * @param String $password
	 * @param String $formUrl
	 * @param String $refer
	 * @return 如果失败，返回false；成功，返回user数组
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
	 * 返回字段:
	 * loanId 借阅的id，续借需要依据它
	 * url 这本书的url
	 * title 书名
	 * author 作者
	 * returnTime 应还时间
	 */
	public abstract function getLoanList();
	
	/**
	 * 获得历史列表
	 * 返回字段：
	 * ordered 当前记录的顺序
	 * url 这本书的url
	 * title 书名
	 * author 作者
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
	
	/**
	 * 生成请求的地址
	 * @param string $schoolNumber 学号
	 * @param int $type 类型： 1为正方管理系统>>我的信息；2为正方管理系统>>我的课表；3为学工管理>>我的基本信息；4为学工管理>>我的宿舍信息
	 */
	public abstract function getRequestUrl($schoolNumber, $type);

	/**
	 * 获得用户对象
	 * @return NULL
	 */
	public function getUser(){
		return null;
	}
	
	
	
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
	
	
	/**
	 * 清除用户登陆的cookie文件
	 */
	public function closeCookie(){
		$fileName = $this->cookieFile;
		if (file_exists($fileName)) {
			@unlink($fileName);
		}
	}
	
	
	
	
	
	
	
}

?>