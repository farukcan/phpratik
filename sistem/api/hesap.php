<?php
	class Hesap {

		static public $sahibi; // false veya o an açık hesapin model şekli
		static public $online = false;

		  static function cache()
		  {
		    return array('kullan');
		  }

		  static function kullan() {
		  	global $__sayfa; // oan işlenn sayfanın kodları

		  	if(strpos($__sayfa, "Model::kullan")===false) // model kullan çağrılmış mı bi bakıver
		  		return "Model::kullan();Hesap::\$sahibi = ".$GLOBALS["__phpratikAyar"]["hesap"]["model"]."::al(".'Oturum::get("hesap_id")'.");Hesap::kontrol();";
		  	else
		  		return "Hesap::\$sahibi = ".$GLOBALS["__phpratikAyar"]["hesap"]["model"]."::al(".'Oturum::get("hesap_id")'.");Hesap::kontrol();";
		  }

		  static function kontrol(){
		  	var_dump(self::$sahibi); // oturumun açık olma olmama durumunu kontrol etmektedir
		  }


		  static function login($username,$password){

		  }

		  static function logout(){

		  }

		  static function setPassword($password){

		  }

		  static function yetkilimi(){
		  	return false;
		  }

	}



	/*

		class Yetki {
			function ver(){
	
			}
			
			function limi($yetki){
				return Hesap::yetkilimi($yetki);
			}

			// mesala

			function adminlik(){
				return Hesap::yetkilimi("adminlik");
			}






		}



	*/