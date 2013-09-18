<?php 
class LibGw extends LibBase{
	
	public $responseHeader = '';
	public $pageContent = '';//保存返回的页面内容
	public $cookie = '';
	
	public function __construct(){
		
	}
	
	
	public function checkField($studentNumber, $password, $formUrl='',$refer=''){
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
	public function saveContent($requestUrl){
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $requestUrl);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch2, CURLOPT_COOKIE, $this->cookie);
		// curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
		
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
		$urilist = $this->getFinalUrl();
		$url = $urilist['url'][1]; 
		$this->saveContent($url);
		$content = $this->getContent();
		$content = $this->escapeNote($content);
		$pattern = $this->getHistoryListRegular();
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
	
	public function getLoanList(){
		$urilist = $this->getFinalUrl();
		$url = $urilist['url'][0]; 
		$this->saveContent($url);
		$content = $this->getContent();
		$content = $this->escapeNote($content);
		$pattern = $this->getLoanListRegular();
		if(preg_match_all($pattern, $content, $matches)){
			$result['id'] = $matches[5];
			$result['author'] = $matches[8];
			$result['url'] = $matches[11];
			$result['title'] = $matches[12];
			$result['publishYear'] = $matches[15];
			$result['returnDate'] = $matches[18];
			$result['payment'] = $matches[21];//这里有点问题，因为我没有欠钱，所以看不到
			$result['location'] = $matches[24];
			$result['callNumber'] = $matches[27];
			return $result;
		}else{
			echo 'gw-->getLoanList';
			return null;
		}
	}
	
	public function renew($bookId){
		
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
	}
	
	
	public function getResponseHeader(){
		return $this->responseHeader;
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
		$libUrl = $result['url'];
		$this->saveContent($libUrl);
		$uriList = $this->parseLibContent($this->getContent()); //2.获取所有的页面url
		return $uriList;
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
	private function parseLibContent($text){
	
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


?>