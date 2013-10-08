<?php
class CoverHelper{
	
	/**
	 * 通过isbn获得封面
	 * @param String $isbn
	 */
	public function getBookByIsbn($isbn){
		
		$searchUrl = 'http://book.douban.com/subject_search?search_text='.$isbn.'&cat=1001';
		$content = file_get_contents($searchUrl);
		
		$pattern = '/<li class=\"subject-item\">(.|\n)*?<img(.)*src=\"(.*?)\"(.|\n)*?<a href=\"(.*?)\"/i';
		if(preg_match($pattern, $content, $match)){
			$result = array(
					'cover'=>$match[3],
					'url'=>$match[5]		
			);
			return $result;
		}else{
			return null;
		}
		
	}

}