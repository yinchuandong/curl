<?php
class Factory{
	
	public static function createClass($className){
		try {
			$reflect = new ReflectionClass($className);
			$gw = $reflect->newInstance();
		} catch (Exception $e) {
			var_dump($e->getMessage());
			$gw = null;
		}
		
		return $gw;
	}
	
}