<?php
class Gwtxz {
	private $pageContent = '';//保存返回的页面内容
	
	public function __construct(){
		
	}
	
	/**
	 * 发送表单数据，并存储cookie
	 * @param array $field //表单的数据
	 * @param string $formUrl //广外统一验证入口 
	 * 学工管理： http://xg.gdufs.edu.cn/pkmslogin.form 
	 * 正方教务： http://jw.gdufs.edu.cn/pkmslogin.form
	 * 
	 */
	function checkField($field, $formUrl=''){
		if (empty($formUrl)){//默认为正方管理系统的验证入口
			$formUrl = 'http://jw.gdufs.edu.cn/pkmslogin.form';
		}
		
		$param = '';
		foreach ($field as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($formUrl);
		
		$header = array(
			'POST http://jw.gdufs.edu.cn/pkmslogin.form HTTP/1.1',
			'Host: '.$host,
			'Connection: keep-alive',
			'Content-Length: '.strlen($param),
			'Cache-Control: max-age=0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Origin: '.$formUrl,
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
			'Content-Type: application/x-www-form-urlencoded',
			'Referer: '.$formUrl,
			'Accept-Encoding: gzip,deflate,sdch',
			'Accept-Language: zh-CN,zh;q=0.8',
			'Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3',
			
		);
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $formUrl);
		// curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); 
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//加了会出错
		// curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		ob_start();
		// 抓取URL并把它传递给浏览器
		curl_exec($ch);
		$cotent = ob_get_clean();
		ob_end_clean();
		
		curl_close($ch);
		
		$pattern ='#<TITLE>Success<\/TITLE>#';
		if(preg_match($pattern, $cotent)){
//			echo 1;
			return true;
		}else{
//			echo 2;
			return false;
		}
	
	}
	
	/**
	 * 保存页面返回内容
	 * @param string $referUrl 请求地址的url
	 * 学工管理：http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/XSJBXX/T_XSJBXX_XSJBB&tfile=XGMRMB/detail_BDTAG
	 * 正方教务: http://jw.gdufs.edu.cn/xskbcx.aspx?xh=20111003632
	 */
	function saveContent($referUrl){
//		$this->checkField($field);
		
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $referUrl);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
	
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
	 * @return string $referUrl
	 */
	public function getReferUrl($schoolNumber, $type){
		$referUrl = '';
		switch ($type){
			case 1:
				$referUrl = 'http://jw.gdufs.edu.cn/xsgrxx.aspx?xh='.$schoolNumber;
				break;
			case 2:
				$referUrl = 'http://jw.gdufs.edu.cn/xskbcx.aspx?xh='.$schoolNumber;
				break;
			case 3:
				$referUrl = 'http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/XSJBXX/T_XSJBXX_XSJBB&tfile=XGMRMB/detail_BDTAG';
				break;
			case 4:
				$referUrl = "http://xg.gdufs.edu.cn/epstar/app/template.jsp?mainobj=SWMS/SSGLZXT/SSAP/V_SS_SSXXST&tfile=XSCKMB/BDTAG&filter=V_SS_SSXXST:XH='".$schoolNumber."'";
				break;
			default:
				$referUrl = 'http://jw.gdufs.edu.cn/xsgrxx.aspx?xh='.$schoolNumber;
				
		}
		return $referUrl;		
	}
	
	function getName($from = 1){
		$content = $this->getContent();
		if ($from ==1 ){
			$pattern ='#(<span id=\"xm\">)(.*)(<\/span>)#';
		}else{
			$pattern = '#(<font id=\"XM\" value=\"(.+)\">)(.*)(<\/font>)#';
		}
		
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
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
		}else{
			$pattern = '#(<font id=\"YX\" value=\"(\w){4,8}\">)(.*)(<\/font>)#';
		}
		
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			return $match[3];
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
		}else{
			$pattern = '#(<font id=\"NJ\" value=\"(\w){4}\">)(.*)(<\/font>)#';
		}
		
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
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
		}else{
			$pattern = '#(<font id=\"BJ\" value=\"(\w){7,10}\">)(.*)(<\/font>)#';
		}
		
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 获得校区
	 * 只能从 学工管理获得    	南校区或北校区
	 * @return mixed null or string
	 */
	function getCampus(){
		
		$pattern = '#(<font id=\"XQ\" value=\"(.+)\">)(.*)(<\/font>)#';
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 获得宿舍楼
	 * 只能从 学工管理获得
	 * @return mixed null or string
	 */
	function getBlock(){
		$pattern = '#(<font id=\"SSL\" value=\"(.+)\">)(.*)(<\/font>)#';
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
		}else{
			return NULL;
		}
	}
	
	/**
	 * 获得房间号
	 * 只能从 学工管理获得
	 * @return mixed null or string
	 */
	function getRoom(){
		$pattern = '#(<font id=\"FJH\" value=\"(\w){2,4}\">)(.*)(<\/font>)#';
		$content = $this->getContent();
		if (preg_match($pattern, $content,$match)) {
			
			return $match[3];
		}else{
			return NULL;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
}