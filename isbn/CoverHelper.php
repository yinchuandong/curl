<?php
class CoverHelper{
	
	public static function getCover($title){
		
		$baseUrl = 'http://www.douban.com/search?q=';
		$searchUrl = $baseUrl.$title;
		$content = file_get_contents($searchUrl);
		echo $content;
	}
	
}