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
	 * @return bool 用户名和密码正确，返回true;否则返回false; 
	 */
	function checkField($studentNumber, $password, $formUrl='',$refer=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = 'http://tsg.gdufs.edu.cn/pkmslogin.form';
		}
		if (empty($refer)){
			$refer = "http://tsg.gdufs.edu.cn/";
		}
		
		$field = array(
				'username'=>$studentNumber,
				'password'=>$password,
				'login-form-type'=> 'pwd', 
		);
		
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
			return true;
		}else{
			return false;
		}
	
	}
	
	/**
	 * 保存页面返回内容,返回服务器端的返回头部，包括redirect_url
	 * @param string $requesUrl 请求地址的url
	 * @return array();
	 */
	function saveContent($requesUrl){
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $requesUrl);
		curl_setopt($ch2, CURLOPT_COOKIE, $this->cookie);
		// curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
	
		ob_start();
		curl_exec($ch2);
		$content = ob_get_contents();
		ob_end_clean();
		
		$info = curl_getinfo($ch2);
		curl_close($ch2);
		$this->pageContent = $content;

		return $info;
	}
	
	
	public function getContent(){
		return $this->pageContent;
	}
	
	/**
	 * 解析登陆之后返回的内容，获得跳转的url和对应的数量,返回一个二位数组
	 * url[0]外借的url, num[0]外接的数量
	 * url[1]借阅历史的url, num[1]借阅的历史数量
	 * url[2]预约请求的url,num[2]预约请求的数量
	 * url[2]预订请求的url,num[2]预定请求的数量
	 * url[2]现金事物的url,num[2]现金事物的数量
	 * @param string $content
	 * @return array
	 */
	public function getFinalUrl($requestUrl=""){
		if (empty($requestUrl)){
			$requestUrl = "http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp";
		}
		$result = $this->saveContent($requestUrl);
		$libUrl = $result['redirect_url'];
		$this->saveContent($libUrl);
		$uriList = $this->parseLibContent($this->getContent()); //2.获取所有的页面url
		return $uriList;
	}
	
	/**
	 * 得到续借的url
	 * 1，http://opac.gdufs.edu.cn:8991/F/T8RKYKUS9U8I63IFIR5HUC7SSSJLBQ7TA8LPE6S5L4YUY3D4AF-01289 解析为：
	 * 	 http://opac.gdufs.edu.cn:8991/F/T8RKYKUS9U8I63IFIR5HUC7SSSJLBQ7TA8LPE6S5L4YUY3D4AF   01289
	 * 2，根据标签的index计算出url
	 * @param string $loanListUrl 当前借阅的列表url
	 * @return $result 部分续借和全部续借的url
	 * 续借部分书名时，还要加上id1=Y&id2=Y
	 */
	public function getRenewUrl($loanListUrl){
		$this->saveContent($loanListUrl);
		$content = $this->getContent();
		$pattern = '/<script\>var tmp=\"(.*)(\-)(.*)\";<\/script>/';
		if (preg_match($pattern, $content,$match)){
			$baseUrl = $match[1];
			$index = $match[3];
			$renewApartUrl = $baseUrl.'-'.sprintf('%05d', $index + 1).'?func=bor-renew-all&renew_selected=Y&adm_library=GWD50';
			$renewAllUrl = $baseUrl.'-'.sprintf('%05d', $index + 16).'?func=bor-renew-all&adm_library=GWD50';
			$result = array(
				'renewApart' => $renewApartUrl, //部分续借
				'renewAll' => $renewAllUrl   	//全部续借
			);
			return $result;
		}else{
			echo 'Library getAuthTmpUrl false';
			return null;
		}
		
		
// 		var_dump($content);
	}
	
	
	
	//=====================一些工具函数============================================
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
	 * 当获取数字广外的内容时，才需要用到
	 * @return string $referUrl 
	 */
	public function getReferUrl(){
		$referUrl = "http://auth.gdufs.edu.cn/wps/myportal/001/00101/!ut/p/c5/fY1LDoIwFADPwgHMe_zLEj-RFhURVNoNqYnBSimEGNTb686dmcUsZjEg4IuRk2rkQ_VGaqhABHWWJ0jnxImynPlI03K7TEjoIgmBgwh_HcliiZSydOOtmItrhGpS1yeUUKFXF_eIbb4-zqOUvYuzmFUmaI-dzspuYEL7r0voH6S-ddLo2diefd7HCyUK0VgWZLaDngu7vZbKQPn3yoH_7YLGp9FxbDtAtAkMLZ8a0lsfBmXNWQ!!/";
		return $referUrl;
	}
	
	

	/**
	 * 解析从上一步获取的图书管界面的内容，获得跳转的url和对应的数量,返回一个二位数组
	 * url[0]外借的url, num[0]外接的数量
	 * url[1]借阅历史的url, num[1]借阅的历史数量
	 * url[2]预约请求的url,num[2]预约请求的数量
	 * url[2]预订请求的url,num[2]预定请求的数量
	 * url[2]现金事物的url,num[2]现金事物的数量
	 * 
	 * @param string $text 图书馆基本信息页面的content
	 * @return array $result = array('url'=>array(),'num'=>array());
	 */
	public function parseLibContent($text){
		
		
		// $pattern = '#<div class="tabcontent" id="history" display="none;">(.*)<\/div>#';
		// $pattern = '/div(.*)id="history"(.*)>(.*)<\/div>/ism';
		
		//$pattern = '(<!--[\w\W\r\n]*?-->)|(<script(.*)>(.|\n)*?<\/script>)';
// 		var_dump($text);
		$noNote = $this->escapeNote($text);
		$noScript = $this->escapeScript($noNote);
		$pattern = '#<div(.*)id="?history"?(.*)>(.|\n)*</div>#';
		$pattern2 = '/<td class="?td1"?>(.*)<\/td>/ism';
		$pattern3 = '/<a href="javascript:replacePage\(\'(.*)\'\);">(.*)<\/a>/i';
		if (preg_match($pattern, $noNote, $match)) {
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
	
	//============获取当前借阅信息==================================
	/**
	 * 获得当前借阅的列表，返回所借阅的书的详细信息，以及归还时间等
	 * 返回一个二维数组,详情如下：
	 * id 书本的id
	 * author 作者
	 * url 书本详情的url
	 * title 书本的标题
	 * publishYear 出版的年限
	 * returnTime 应该归还的时间
	 * payment 欠了多少钱
	 * location 哪个图书馆借的（南校或者北校)
	 * searchNumber 索书号，该书在图书馆的哪个架子上
	 * @param $content
	 * @return array
	 */
	public function getLoanList($url){
		$this->saveContent($url);
		$content = $this->getContent();
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
	private function getLoanListRegular(){
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
	
	//===========获取借阅历史表===================================================
	/**
	 * 获取借阅历史的列表，返回一个二位数组，详情如下：
	 * url 书本详细信息的url
	 * author 作者
	 * title 标题
	 * publishYear 出版年限
	 * limitDate 应还日期
	 * limitTime 应还时间
	 * returnDate 归还日期
	 * returnTime 归还时间
	 * payment 欠了多少钱
	 * location 南校或北校图书馆
	 * @param unknown_type $content
	 * @return unknown|NULL
	 */
	public function getHistoryList($url){
		$this->saveContent($url);
		$content = $this->getContent();
		$content = $this->escapeNote($content);
		$pattern = $this->getHistoryListRegular();
// 		echo $content;
		if(preg_match_all($pattern, $content, $matches)){
			$result['url'] = $matches[5];
			$result['author'] = $matches[6];
			$result['title'] = $matches[10];
			$result['publishYear'] = $matches[13];
			$result['limitDate'] = $matches[16];
			$result['limitTime'] = $matches[19];
			$result['returnDate'] = $matches[22];
			$result['returnTime'] = $matches[25];
			$result['payment'] = $matches[28];
			$result['location'] = $matches[31];
			return $result;
		}else{
			echo 'getHistoryList_false';
			return null;
		}
		
	}
	
	/**
	 * 获取借书历史记录的正则表达式
	 * @return string
	 */
	private function getHistoryListRegular(){
		$regular = '';
		//获得第一个通配符
		$regular .= '<td class=td1 id=centered(.*)>(.)*<\/td>(.|\n)*?';
		//获取作者
		$regular .= '<td class=td1(.)*><a href="(.*)" target=_blank>(.*)<\/a><\/td>(.|\n)*?';
		//标题
		$regular .= '<td class=td1(.)*><a href="(.*)" target=_blank>(.*)<\/a><\/td>(.|\n)*?';
		//出版时间
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//应还日期
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//应还时间
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//归还日期
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//归还时间
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//罚款
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		//在哪个图书馆
		$regular .= '<td class=td1(.)*>(.*)<\/td>(.|\n)*?';
		return '/'.$regular.'/i';
	}
	
	//===================公共函数====================================
	/**
	 * 去除html注释
	 * @param string $text
	 */
	private function escapeNote($text){
		return preg_replace('/<!--[\w\W\r\n]*?-->/i', "", $text);
	}
	
	/**
	 * 去除script标签
	 * @param string $text
	 * @return string
	 */
	private function escapeScript($text){
		return preg_replace('/<script(.*)>(.|\n)*?<\/script>/i', "", $text);
	}
	
	
	
	
	
	
	
}