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
	 * @return string
	 */
	public abstract function saveContent();
	
	/**
	 * 获得借阅列表
	 */
	public abstract function getLoanList();
	
	/**
	 * 获得历史列表
	 */
	public abstract function getHistoryList();
}

?>