<?php 
define(__UTIL__, dirname(dirname(__FILE__)).'/util');
include __UTIL__.'/simple_html_dom.php';
class LibGw extends LibBase{
	
	private $responseHeader = '';
	private $pageContent = '';//保存返回的页面内容
	
	/**
	 * 保存从页面解析的cookie
	 */
	protected  $cookie = '';
	/**
	 * 文件名：保存通过curl自带的解析cookie，可用可不用
	 * */
	protected $cookieFile = '';
	private $firstContent = '';//不知道这个干什么用
	/**
	 * 登陆成功后返回的url
	 * */
	private $libUrl = '';
	
	public function __construct(){
		
	}
	
	/**
	 * 
	 * 检查教务系统的用户名和密码
	 * 
	 * */
	private function checkUser($studentNumber, $password, $formUrl='',$refer=''){
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
		
		//将cookie写入文件中
		$this->cookieFile = dirname(__FILE__).'/cookie/'.'LibGw'.$studentNumber.'.txt';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
// 		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//跟随跳转
		
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
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
	 * 
	 * 检查图书馆系统
	 * */
	public function checkField($studentNumber, $password, $formUrl='',$refer=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = 'http://lib.gdufs.edu.cn/bor_rec.php';
		}
		if (empty($refer)){
			$refer = "http://tsg.gdufs.edu.cn/";
		}
	
		$field = array(
				'userid'=>$studentNumber,
				'userpwd'=>$password,
				'imageField.x'=> '36',
				'imageField.y'=> '2'
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
	
		//将cookie写入文件中
		$this->cookieFile = dirname(__FILE__).'/cookie/'.'LibGw'.$studentNumber.'.txt';
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		// 		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//跟随跳转
	
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		
		curl_close($ch);
		$this->parseResponseCookie($content);//从返回的内容中提取出cookie
		$body = substr($content, $header_size);
		
		//可以分离头部和Body部分
// 		$arr = explode("\r\n\r\n", $content, 2);
// 		file_put_contents("./testlib_content.txt", $content);

		$pattern ='#url=uindex.php#';
		if(preg_match($pattern, $content)){
			$header = substr($content, 0, $header_size);
// 			file_put_contents("./testlib_content2.txt", $header);
			$content = explode("\r\n", $header);
			if (preg_match('#bor_url=(.+)#', $header, $match)){
				$this->libUrl = urldecode($match[1]);
			}else{
				return false;
			}
			$this->checkUser($studentNumber, $password);
			$userInfo = $this->getUserInfo($studentNumber);
// 			var_dump($userInfo);
// 			$this->getUser();
			return $userInfo;
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
	
	/**
	 * 从学工系统中获得用户信息
	 * @param unknown $studentNumber
	 * @return Ambigous <mixed, NULL, unknown>
	 */
	public function getUserInfo($studentNumber){
		$url = $this->getRequestUrl($studentNumber,3);
		$this->saveContent($url);
		
		$userInfo['username'] =  $this->getName(3);
		$userInfo['academy'] = $this->getAcademy(3);
		$userInfo['grade'] = $this->getGrade(3);
		$userInfo['major'] = $this->getMajor(3);
		if ($userInfo['username'] != NULL) {
			return $userInfo;
		}
		$requestUrl = "http://tsg.gdufs.edu.cn/gwd_local/login_ibm.jsp";
		$result = $this->saveContent($requestUrl);
		$pattern = "/<tr>(.|\n)*?<\/tr>/i";
		if (preg_match($pattern, $this->getContent(), $matches)) {
			$str = strip_tags($matches[0]);
			$qian=array(" ","　","\t","\n","\r","姓名");
			$hou=array("","","","","");
			$str = str_replace($qian,$hou,$str);
			$userInfo['username'] =  $str;
		}
		return $userInfo;
	}
	
	
	public function getHistoryList(){
		$urilist = $this->getFinalUrl($this->libUrl);
		$url = $urilist['url'][1];
		$this->saveContent($url);
		$content = $this->getContent();
		$content = $this->escapeNote($content);
		$pattern = $this->getHistoryListRegular();
		if(preg_match_all($pattern, $content, $matches)){
// 			var_dump($matches[3]);die;
			$result['order']= $matches[3];
			$result['url'] = $matches[6];
			$result['author'] = $matches[7];
			$result['title'] = $matches[11];
			$result['publishYear'] = $matches[14];
			$result['limitDate'] = $matches[17];
			$result['limitTime'] = $matches[20];
			$result['returnDate'] = $matches[23];
			$result['returnTime'] = $matches[26];
			$result['payment'] = $matches[29];
			$result['location'] = $matches[32];
			return $result;
		}else{
			return null;
		}
	}
	
	public function getLoanList(){
		$urilist = $this->getFinalUrl($this->libUrl);
		$url = $urilist['url'][0]; 
		$this->saveContent($url);
		$content = $this->getContent();
		$content = $this->escapeNote($content);
		$pattern = $this->getLoanListRegular();
		if(preg_match_all($pattern, $content, $matches)){
			$result['loanId'] = $matches[5];
			$result['author'] = $matches[8];
			$result['url'] = $matches[11];
			$result['title'] = $matches[12];
			$result['publishYear'] = $matches[15];
			$result['returnTime'] = $matches[18];
			$result['payment'] = $matches[21];//这里有点问题，因为我没有欠钱，所以看不到
			$result['location'] = $matches[24];
			$result['callNumber'] = $matches[27];
			return $result;
		}else{
			return null;
		}
	}
	
	
	public function renew($bookId){
		$uriList = $this->getFinalUrl($this->libUrl);
		$url = $this->getRenewUrl($uriList['url'][0]);
		$url = $url['renewApart'];
		$url .= '&'.$bookId.'=Y';
		$this->saveContent($url);
		$content = $this->getContent();
		$pattern = $this->getRenewRegular();
		if (preg_match('/<div class=title>(.|\n)*?([\s\S]*)<\/div>/i', $content, $match)){
			$result = explode("-", strip_tags(trim($match[2])));
			$result = preg_replace("/(\:|\n)/i", "", $result[1]);
			$result = trim($result);
			if($result == "续借不成功"){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
		
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
// 			echo 'Library getAuthTmpUrl false';
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
		$pattern3 = '/<a href="javascript:replacePage\(\'(.*)\'\);">(.*)<\/a>/i';
		
		if (preg_match_all($pattern3, $noNote, $match3)) {
			// 			var_dump($match3[2]);
			$result = array(
					'url' => $match3[1],
					'num' => $match3[2]
			);
			return $result;
		}else{
			return false;
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
		$regular .= '<td class=td1 id=centered(.*)\><A href=(.*)>(.*)<\/A><\/td>(.|\n)*?';
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
	
	/**
	 * 获得续借返回信息的正则表达式
	 */
	private function getRenewRegular(){
		$regular = '/<div class=title>(.|\n)*?<\/div>/i';
		return $regular;
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
	
	
	function getName($from = 1){
		$content = $this->getContent();
		if ($from ==1 ){
			$pattern ='#(<span id=\"xm\">)(.*)(<\/span>)#';
		}else{
			$pattern = '#(<font id=\"XM\" value=\"(.+)\">)(.*)(<\/font>)#';
		}
	
		if (preg_match($pattern, $content,$match)) {
			return $match[2];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 从哪里获取学号
	 * @param int $from 1为正方管理系统；2为学工管理
	 * @return mixed null or string
	 */
	function getStudentNumber($from=1){
		if ($from ==1 ){
			$pattern ='#(<span id=\"xh\">)(.*)(<\/span>)#';
		}else{
			$pattern = '#(<font id=\"XH\" value=\"(\d){10,13}\">)(.*)(<\/font>)#';
		}
	
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
				
			return $match[3];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 从哪里获取学院
	 * @param int $from 1为正方管理系统；2为学工管理
	 * @return mixed null or string
	 */
	function getAcademy($from = 1){
		if ($from ==1 ){
			$pattern ='#(<span id=\"lbl_xy\">)(.*)(<\/span>)#';
			$position = 2;
		}elseif($from == 3){
			$pattern = '#(<font id=\"YXSH\" value=\"(\w){4,8}\">)(.*)(<\/font>)#';
			$position = 3;
		}else{
			$pattern = '#(<font id=\"YX\" value=\"(\w){4,8}\">)(.*)(<\/font>)#';
			$position = 3;
		}
		$content = $this->getContent();
		if (preg_match($pattern, $content, $match)) {
			return $match[$position];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 从哪里获取年级
	 * @param int $from 1为正方管理系统；2为学工管理
	 * @return mixed null or string
	 */
	function getGrade($from = 1){
		if ($from ==1 ){
			$pattern ='#(<span id=\"lbl_dqszj\">)(.*)(<\/span>)#';
			$position = 2;
		}elseif( $from == 3){
			$pattern = '#(<font id=\"XZNJ\" value=\"(\w){4}\">)(.*)(<\/font>)#';
			$position = 3;
		}else{
			$pattern = '#(<font id=\"NJ\" value=\"(\w){4}\">)(.*)(<\/font>)#';
			$position = 3;
		}
	
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			return $match[$position];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 从哪里获得专业
	 * @param int $from 1为正方管理系统；2为学工管理
	 * @return mixed null or string
	 */
	function getMajor($from = 1){
		if ($from ==1 ){
			$pattern ='#(<span id=\"lbl_xzb\">)(.*)(<\/span>)#';
			$position = 2;
		}elseif($from == 3){
			$pattern = '#(<font id=\"ZYDM\" value=\"(\w){1,10}\">)(.*)(<\/font>)#';
			$position = 3;
		}else{
			$pattern = '#(<font id=\"BJ\" value=\"(\w){7,10}\">)(.*)(<\/font>)#';
			$position = 3;
		}
	
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			return $match[$position];
		}else{
			return NULL;
		}
	}
	
	
}
?>