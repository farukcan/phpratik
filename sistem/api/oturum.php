<?php

	class Oturum {
		
		static private $kaydedici; // en son sayfada iş bitince kaydeder
		static public $ip;

		// Oturum verilerin durumu kontrol eder, ve oturum sistemini aktifleştiri
		static function check() {

			if(isset($GLOBALS["_OTURUM"])) return $GLOBALS["phpratik_oturum_kodu"];

			// bu class destruct olurken; eski oturum dosyası silinip yenisine yazılır

			self::$kaydedici = new OturumKaydedici();

			self::$ip = self::ip();

			if(isset($_COOKIE['phpratik'])){

				Oturum::isValid($_COOKIE['phpratik']);

			}
				// cookie yoksa
				return Oturum::create();
			


		}
		static function kullan() { return self::check(); }

		// Oturum oluşturur. Her seferinde , eski veriler varsa bile yenilenmektedir.
		static function create(){
			if(!isset($GLOBALS["_OTURUM"])){
				$GLOBALS["_OTURUM"] = array();
				$GLOBALS["_OTURUM"]["ip"] = Oturum::ip();
				$GLOBALS["_OTURUM"]["user-agent"] = $_SERVER['HTTP_USER_AGENT'];
				$GLOBALS["_OTURUM"]["creation-time"] = time();
			}

			$GLOBALS["_OTURUM"]["zaman"] = time();

			$GLOBALS["phpratik_oturum_kodu"] = Oturum::generate();
			setcookie("phpratik",$GLOBALS["phpratik_oturum_kodu"],time()+$GLOBALS['__phpratikAyar']['oturum']['zaman'],$GLOBALS['__phpratikAyar']['oturum']['yol']);
			return $GLOBALS["phpratik_oturum_kodu"];
		}

		// eşsiz bir kod oluştur.
		static function generate(){
			return sha1(Oturum::ip().uniqid());
		}

		static function load($kod){
			// dosyadan mı?
			// veri tabanından mı?
			// Mcache'den mi?
			switch ($GLOBALS['__phpratikAyar']['oturum']['motor']) {
				case 'dosya':
					$file = $GLOBALS['__phpratikAyar']['oturum']['klasor']."/".$kod.".dat";

					if(is_file(ROOT.$file))
						return okuDosya($file);
					else
						return false;
					break;


				case 'mcache':
					return Mcache::get($GLOBALS['__phpratikAyar']['oturum']['soyad_'].$kod);
					break;


				case 'vt':
					$data = Vt::sorgu()->tablo('oturum')->nerede([
						$GLOBALS['__phpratikAyar']['oturum']['kodSutunu'] => $kod
						])->hepsiniAl(true);

					if(count($data)==0) return false;
					else return $data[0][$GLOBALS['__phpratikAyar']['oturum']['veriSutunu']];
					break;

				default:
					return false;
					break;
			}
		}

		// dosyaya kaydeder
		static function kaydet(){
			switch ($GLOBALS['__phpratikAyar']['oturum']['motor']) {
				case 'dosya':
					if(isset($GLOBALS["_OTURUM"]["kod"])){
						// eski oturum dosyasını sil

						$file = ROOT.$GLOBALS['__phpratikAyar']['oturum']['klasor']."/".$GLOBALS["_OTURUM"]["kod"].".dat";

						if(is_file($file))
							unlink($file);
					}


					// yeni oturum dosyası oluştur
					$file = $GLOBALS['__phpratikAyar']['oturum']['klasor']."/".$GLOBALS["phpratik_oturum_kodu"].".dat";
					$GLOBALS["_OTURUM"]["kod"] = $GLOBALS["phpratik_oturum_kodu"];
					yazDosya($file,serialize($GLOBALS["_OTURUM"]));

					break;

				case 'mcache':
					if(isset($GLOBALS["_OTURUM"]["kod"]))
						Mcache::delete($GLOBALS['__phpratikAyar']['oturum']['soyad_'].$GLOBALS["_OTURUM"]["kod"]);

					$GLOBALS["_OTURUM"]["kod"] = $GLOBALS["phpratik_oturum_kodu"];

					Mcache::set($GLOBALS['__phpratikAyar']['oturum']['soyad_'].$GLOBALS["phpratik_oturum_kodu"],serialize($GLOBALS["_OTURUM"]));
					
					break;

				case 'vt':
					if(isset($GLOBALS["_OTURUM"]["kod"]))
						Vt::sorgu()->tablo($GLOBALS['__phpratikAyar']['oturum']['tablo'])->nerede(
						 $GLOBALS['__phpratikAyar']['oturum']['kodSutunu'] ,"=", $GLOBALS["_OTURUM"]["kod"]
						 )->sil();

					$GLOBALS["_OTURUM"]["kod"] = $GLOBALS["phpratik_oturum_kodu"];
					
					Vt::sor()->tablo($GLOBALS['__phpratikAyar']['oturum']['tablo'])->ekle([
						$GLOBALS['__phpratikAyar']['oturum']['kodSutunu'] => $GLOBALS["phpratik_oturum_kodu"],
						$GLOBALS['__phpratikAyar']['oturum']['veriSutunu'] => serialize($GLOBALS["_OTURUM"])
					]);



					break;

			}
		}

		// buda yükler
		static function isValid($kod){
			// oturumun kodunun geçerliliğini kontrol et
			// oturum kodu ile ip geçerliliklerini kontrol et
			// oturum bilgilerini yükle

			if(strlen($kod)!=40) return false;

			$load = Oturum::load($kod); // veriyi yükle

			if($load===false) return false;


			$GLOBALS['_OTURUM'] = unserialize($load);

			if($GLOBALS['_OTURUM']["ip"] != self::$ip) return false;

			return true;
		}

		static function kod(){
			return $GLOBALS["phpratik_oturum_kodu"];
		}

		static function bilgi($veri){
			if(!isset($GLOBALS["_OTURUM"])) Oturum::check();
			return isset($GLOBALS["_OTURUM"][$veri]) ? $GLOBALS["_OTURUM"][$veri] : false;
		}
		static function get($v) { return Oturum::bilgi($v); }
		static function al($v) { return Oturum::bilgi($v); }


		static function yaz($degisken,$veri){
			if(!isset($GLOBALS["_OTURUM"])) Oturum::check();
			$GLOBALS["_OTURUM"][$degisken] = $veri;
		}
		static function set($v,$d) { return Oturum::yaz($v,$d); }

		static function ip(){
		    if(getenv("HTTP_CLIENT_IP")) {
		         $ip = getenv("HTTP_CLIENT_IP");
		     } elseif(getenv("HTTP_X_FORWARDED_FOR")) {
		         $ip = getenv("HTTP_X_FORWARDED_FOR");
		         if (strstr($ip, ',')) {
		             $tmp = explode (',', $ip);
		             $ip = trim($tmp[0]);
		         }
		     } else {
		     $ip = getenv("REMOTE_ADDR");
		     }
		    return $ip;
		}


	}


	class OturumKaydedici
	{
	  public function __destruct()
	  {
	    Oturum::kaydet();
	  }
	}
