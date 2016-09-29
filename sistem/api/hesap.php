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

		  	$ek = "";
		  	if(strpos($__sayfa, "Model::kullan")===false) // model kullan çağrılmış mı bi bakıver
		  		$ek .= "Model::kullan();";
		  	if(strpos($__sayfa, "Oturum::kullan")===false) // oturum kullan çağrılmış mı bi bakıver
		  		$ek .= "Oturum::kullan();";

		  	return $ek."Hesap::\$sahibi = ".$GLOBALS["__phpratikAyar"]["hesap"]["model"]."::al(".'Oturum::get("hesap_id")'.");Hesap::kontrol();";
		  }

		  static function kontrol(){
		  		self::$online = !(self::$sahibi === false);
		  		return self::$online;
		  }


		  static function login($username,$password){
		  		if(!self::$online){
		  			$m = $GLOBALS["__phpratikAyar"]["hesap"]["model"];
		  			$u = $GLOBALS["__phpratikAyar"]["hesap"]["usernameColumn"];
		  			$p = $GLOBALS["__phpratikAyar"]["hesap"]["passwordColumn"];

		  			if( $GLOBALS["__phpratikAyar"]["hesap"]["hash"] == "sha1" )
		  				$pass = sha1($password);
		  			else if( $GLOBALS["__phpratikAyar"]["hesap"]["hash"] == "md5" )
		  				$pass = md5($password);
		  			else
		  				$pass = $password;

		  			$user = eval("return ".$m."::tekiniBul('$u = ? AND $p = ?',array('$username','$pass'));");
		  			if($user === false){
		  				return false;
		  			}else{
		  				self::$sahibi = $user;
		  				self::kontrol();
		  				Oturum::set("hesap_id",$user->id);
		  			}

		  			return $user;

		  		}else return array(
		  				"ok" => false,
		  				"err" => "Already Online"
		  			);

		  }

		  static function logout(){
		  		if(self::$online){
		  			Oturum::set("hesap_id",null);
		  			self::$sahibi = false;
		  			self::kontrol();
		  		}else return array(
		  				"ok" => false,
		  				"err" => "Already Offline"
		  		);
		  }


		  static function yetkilimi($yetki){
		  	if(self::$online){
		  		$yetkiler = explode(",", eval("return Hesap::\$sahibi->".$GLOBALS["__phpratikAyar"]["hesap"]["yetkiColumn"].";"));
		  		
		  		foreach ($yetkiler as $y) {
		  			if($y === $yetki)
		  				return true;
		  		}
		  	}
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