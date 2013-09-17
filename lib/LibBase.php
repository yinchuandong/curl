<?php 
interface LibBase {
	/**
	 * 检查用户名密码是否正确
	 * @param String $studentNumber
	 * @param String $password
	 * @param String $formUrl
	 * @param String $refer
	 * @return bool
	 */
	public function checkField($studentNumber, $password, $formUrl='',$refer='');	
	
	/**
	 * 保存页面内容
	 * @param $requestUrl
	 * @return string
	 */
	public function saveContent($requestUrl);
	
	/**
	 * 获得借阅列表
	 */
	public function getLoanList();
	
	/**
	 * 获得历史列表
	 */
	public function getHistoryList();
	
	/**
	 * 得到页面返回的内容
	 * @return string
	 */
	public function getContent();
	
	/**
	 * 得到responseHeader信息
	 * @return array
	 */
	public function getResponseHeader();
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>