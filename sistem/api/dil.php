<?php

class Dil {
	static function str($deger)
	{
		return self::check().'$GLOBALS[\'__phpratikDil\'][\''.$deger.'\'][$__phpratikDil_id]';
	}

	static function check(){
		global $__sayfa; // oan işlenn sayfanın kodları
		$checkIsUsed =  isset($GLOBALS["__dilUsed"]);
		if($checkIsUsed) return "";

		$return = "";
		$GLOBALS["__dilUsed"] = "yes!";

		// csv dosyasını oku
		$lines = explode("\n", okuDosya($GLOBALS["__phpratikAyar"]["dil"]["dosya"]));
		$out = array();

		foreach ($lines as $line) {
			$a = str_getcsv($line,";");
			$out[$a[0]] = array_slice($a,1); 
		}

		// değişken haline getir
		$return .= '$GLOBALS[\'__phpratikDil\'] = ';
		$return .= var_export($out,true).";";

		// default dil ilk sutundakidir.
		$return .= '$__phpratikDil_id = 0;';
		$return .= "Dil::kontrol();";

		$__sayfa = "<?php\n".$return."?>".$__sayfa;

		return "";
	}

	static function kontrol(){
		global $__phpratikDil_id;
		if($GLOBALS["__phpratikAyar"]["dil"]["oturum"]){
			// oturumda kullanıcı dili kaydedilsin mi
			if(!is_bool(Oturum::get("_dil") )) self::_set(Oturum::get("_dil")); // oturum da var açıldıysa
			else if($GLOBALS["__phpratikAyar"]["dil"]["browser"] ){ // yoksa tarayıcıdan al
				self::_setFromBrowser();
			}
		}else if($GLOBALS["__phpratikAyar"]["dil"]["browser"]){
			self::setFromBrowser();
		}
	}

	static function _set($dil){
		global $__phpratikDil_id;
		foreach ($GLOBALS['__phpratikDil']['lang_code'] as $id => $d) {
			if($d === $dil){
				$__phpratikDil_id = $id;
				if($GLOBALS["__phpratikAyar"]["dil"]["oturum"])
					Oturum::set("_dil",$d);

				return true;
			}
		}

		return false;
	}

	static function _setFromBrowser(){
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		return self::_set($lang);
	}


	static function set($dil){
		return self::check()."Dil::_set('$dil')";
	}

	static function setFromBrowser($dil){
		return self::check()."Dil::_setFromBrowser()";
	}
    function cache()
    {
        return array('str','set','setFromBrowser');
    }
}