<?php
	/*
	|--------------------------------------------------------------------------
	| Girdi
	|--------------------------------------------------------------------------
	|	GET/POST isteklerini ve URL üzerindeki istekleri düzenler
	| 
	*/

class Girdi {
	static function varsa($girdiAd)
	{
		if(isset($_GET[$girdiAd]) || isset($_POST[$girdiAd])) return true;
		return false;
	}
	static function al($girdiAd)
	{
		if(Girdi::metod($girdiAd)) return $_POST[$girdiAd];
		else return $_GET[$girdiAd];
	}
	static function metod($girdiAd){
		if(isset($_POST[$girdiAd])) return true;
		return false;
	}
	static function gerekliGET($arr)
	{
		$GLOBALS['__gerekliGET'] = $arr;
		foreach ($arr as $girdi) {
			if(!isset($_GET[$girdi])){
				require_once 'sistem/fonksiyon/400.php';
				break;
			}
		}
	}
	static function gerekliPOST($arr)
	{
		$GLOBALS['__gerekliPOST'] = $arr;
		foreach ($arr as $girdi) {
			if(!isset($_POST[$girdi])){
				require_once 'sistem/fonksiyon/400.php';
				break;
			}
		}
	}


	static function hepsiniAl()
	{
		// GET veya POST ile alınanları GLOBAL değişken yapar
		if(isset($GLOBALS['__gerekliGET']))
			foreach ($GLOBALS['__gerekliGET'] as $girdi) {
				$GLOBALS[$girdi] = $_GET[$girdi];
			}
		if(isset($GLOBALS['__gerekliPOST']))
			foreach ($GLOBALS['__gerekliPOST'] as $girdi) {
				$GLOBALS[$girdi] = $_POST[$girdi];
			}
	}


}

