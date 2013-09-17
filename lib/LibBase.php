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
}

?>