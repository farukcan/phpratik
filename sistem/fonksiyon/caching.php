<?php

	$isCaching = true;
	$__sayfa=""; // mevcut işlenen sayfanın kodudur. apilerin cache fonksiyonları için

	/*
	|--------------------------------------------------------------------------
	| APiLER HAKKINDAKI BİLGİLERİN YÜKLENMESİ
	|--------------------------------------------------------------------------
	|
	| 
	*/

		$api=array(); // bu değişkende apileri ve bilgilerini saklıyoruz
		foreach (getFilenames(ROOT.'/sistem/api','.php') as $apiName) {
			$api[$apiName] = json_decode(okuDosya('sistem/api/'.$apiName.'.json'));
			$api[$apiName]->class=ucwords($apiName);
			//require_once '/sistem/api/'.$apiName.'.php';
		}
		//echo '<h1>Apiler</h1>';
		//var_dump($api);


	/*
	|--------------------------------------------------------------------------
	|  HER BİR ROTANIN TEK TEK OLUŞTURULMASI
	|--------------------------------------------------------------------------
	|
	| 
	*/

		$kontroller=array(); // kontrollerları defalarca tekrar yüklememe amaçlı
		$rotaData=a2c($app->loadRota());	
		$degiskensizRotalar = array();
		$degiskenliRotalar = array();
		foreach ($rotaData->rotalar as $rotas) {
			// bu for içinde her rotayı ayrı ayrı işleyeceğiz
			// kontrol yükle OK
			// validasyon hallet OK
			$rota = a2c($rotas); // class olarak gör

			$addapi = array(); //eklenecek apiler

			/*
			|--------------------------------------------------------------------------
			| ROTANIN VALİDASYON BİLGİLERİNİN İŞLENMESİ
			|--------------------------------------------------------------------------
			|
			| 
			*/

			
			$validasyonData=''; // eklenecek validasyon verisi
			// GET gereklilikleri
			if(count($rota->get)>0){
				$validasyonData.='Girdi::gerekliGET(array('.'"'.$rota->get[0].'"';
				for ($i=1; $i < count($rota->get); $i++) { 
					$validasyonData.=',"'.$rota->get[$i].'"';
				}
				$validasyonData.='));';		
			}

			//POST gereklilikleri
			if(count($rota->post)>0){
				$validasyonData.='Girdi::gerekliPOST(array('.'"'.$rota->post[0].'"';
				for ($i=1; $i < count($rota->post); $i++) { 
					$validasyonData.=',"'.$rota->post[$i].'"';
				}
				$validasyonData.='));';			
			}

			//API validasyonları			
			foreach ($rota->validasyon as $value) {
				$validasyonData.=str_replace('@', '::', $value).'();';
			}


			/*
			|--------------------------------------------------------------------------
			| ROTANIN KONTROLUNUN KODLARININ İŞLENMESİ
			|--------------------------------------------------------------------------
			|
			| 
			*/

			// kontrol yükleme
			if(!isset($kontroller[$rota->kontrol]))
				$kontroller[$rota->kontrol] = okuDosya('kontrol/'.$rota->kontrol.'.php');


			// tag ları parçalara ayırma
			$sayfa =  '<?php '.$validasyonData.' ?>'.$kontroller[$rota->kontrol];
			

			// Burada kontrollerin, class kullanımına bakılarak , eklenmesi gereken classlar belirlenir
			$apicache=true; // AMAÇ :: her bir classın kontrol koduna müdahele bulunması, yeni classların işe dahil olması durumunda en başta işlemlere devam edilmesi
			
			$__phpratikAyar = array();

			while($apicache){ // apiler cache üretiminde bulunursa
				$apicache=false;


				// kodları taglara göre ayır
				$rotaFileData = str_replace(array('<?php','?>'), '@#%PHP%#@', $sayfa);
				$xx=explode('@#%PHP%#@', $rotaFileData); //tek key noya sahipler PHP kodudur



					/*
					|--------------------------------------------------------------------------
					| KONTROL SAYFASINDA KULLANILAN APILERİN TESPİTİ
					|--------------------------------------------------------------------------
					|
					| 
					*/
				
				foreach ($api as $apiName => $apiData) {
					if(isset($addapi[$apiName])) continue;
					foreach ($xx as $s => $data) {
						if($s%2==1){ // php tagları arasında api kullanımlarını ara

							if(	!is_bool(strpos($data,$apiData->class.'::'))  
								|| !is_bool(strpos($data,'new '.$apiData->class)) 
								)
							{

									$addapi[$apiName]=true; // apiyi ve onun gerekli apilerini ekle
									foreach ($apiData->gerekli_apiler as $gapi)
											$addapi[$gapi]=true;


									break;
							}
								
								
						}
					} // foreach sonu
				}//foreach sonu

					/*
					|--------------------------------------------------------------------------
					| KODLARI GERİ BİRLEŞTİRME
					|--------------------------------------------------------------------------
					|
					| 
					*/
				
				$sayfa='';
				foreach ($xx as $s => $data) {
					if($s%2==0) $sayfa.=$data; // html kısmı
					else $sayfa.=compress_php_src("<?php".$data.'?>'); // php kısmı
				}



					/*
					|--------------------------------------------------------------------------
					| APiLERİN CACHE FONKSİYONLARINI ÇAĞIRMA
					|--------------------------------------------------------------------------
					|
					| 
					*/
				
				// buradan sonra api cacheleri çağrılırmak için yükle ve çağır teker teker
				foreach (array_keys($addapi) as $s) {
					if($api[$s]->ayar) //ayar dosyası varsa ekle
						$__phpratikAyar[$s] = $app->loadAyar($s); // PERFORMSN AÇISINDAN SIKINTILI
					if($api[$s]->cache){ // yüklenecek apinin cachesi varsa
						require_once 'sistem/api/'.$s.'.php'; // classını yükle
						$return=cache($api[$s],$sayfa); // app, cache işleme fonksiyonuna gönder
						$sayfa = $return[0]; // sayfanın yenideğeri dizin ilk değeri
						if($return[1]) // bir değişiklik yapıldı mı ?
							$apicache=true; 
					}

				}//foreach sonu
				

				
					
			}// while sonu


			
			//echo '<h1>Eklenecek Apiler</h1>';
			//var_dump($addapi);
			// geri birleştirme ?

			// Classları ve ayarlarını sayfa başına ekle __phpratikAyar


			

			foreach (array_keys($addapi) as $apiAd) {
				if(!$api[$apiAd]->cacheonly)
					$sayfa=compress_php_src(okuDosya('sistem/api/'.$apiAd.'.php')."?>").$sayfa;
			}

			$sayfa= compress_php_src('<?php $__phpratikAyar='.var_export($__phpratikAyar,true).';?>').$sayfa;

			

			// KOD OPTİMİZASYONu YAP

			//echo '<h1>Cikan Kontrol</h1><pre>'.$sayfa;
			

			// ROTA ayarlamaları


			// rota cacheini kaydet

			yazDosya('sistem/cache/rota/'.sha1($rota->url),$sayfa);


			$rotaCount =count(url_oku($rota->url,$rota->url));
			if($rotaCount===0)
				array_push($degiskensizRotalar, $rota->url); //cdeğişken sayısı 0 ise
			else
				$degiskenliRotalar[$rota->url]=$rotaCount;
				//array_push($degiskenliRotalar, $rota->url); //cdeğişken sayısı 0dan fazla ise
			//echo $rota->url;


		}//kontrol foreachi sonu

		// burda cache/index.php oluşturulur
		//- genel.php yi al ve bura yaz
		arsort($degiskenliRotalar);
		$indexcache = okuDosya('sistem/ayar/genel.php');
		$indexcache .= '$_degiskensiz='.var_export($degiskensizRotalar,true).';';
		$indexcache .= '$_degiskenli='.var_export(array_keys($degiskenliRotalar),true).';';
		yazDosya('sistem/cache/index.php',$indexcache);

		yazDosya('sistem/cache/dosyalar.dat',serialize($yeni_zamanlar));


		$app = new App;



	function cache($api,$sayfa)
	{
		global $__sayfa;

		$__sayfa = $sayfa;

		$cached=false;
		$called_class = new $api->class(); // apinin classını çağır
		// APInın classını kendine dahil et
		// apinin cache fonksiyonlarının isimlerini al
		$class_name = $api->class;

		// her cache fonksiyonu için yap ;
		foreach ($called_class->cache() as $class_function) {
			// her fonksiyonu tek tek ara ve işle
				// sayfada o classının fonksiyonun arayalım
				preg_match_all("/$class_name::$class_function\((.*?)\)/", $__sayfa, $output_array);
				// amaç x(a) ile x(b) leri ayırmak

				// bulursak onları bir arraye alalım
				$datas = array_unique($output_array[1]);
				//var_dump($datas);
				// o array üzerinde fonksiyonu çağırarak tek tek kod üzerinde değişiklik yapalım
				foreach ($datas as $fundata) {
					$__sayfa = str_replace(
							"$class_name::$class_function($fundata)", 
							include cached_function("$class_name::$class_function($fundata)"), $__sayfa);
					$cached = true;
				}
		}

		return array($__sayfa,$cached);
	}


function cached_function($fonksiyonSTR)
{
	$file = "sistem/cache/fonksiyon/".sha1($fonksiyonSTR);
	// cache fonksiyonu çalışırken kendi için dosya oluşturur ve bu dosya include edilecek çalıştırılır.
	
	// daha önce oluşturulduysa
	if(is_file($file)) return $file;

	yazDosya($file,	"<?php
	return $fonksiyonSTR ;
");

	return $file;
}



function compress_php_src($src) {
    // Whitespaces left and right from this signs can be ignored
    static $IW = array(
        T_CONCAT_EQUAL,             // .=
        T_DOUBLE_ARROW,             // =>
        T_BOOLEAN_AND,              // &&
        T_BOOLEAN_OR,               // ||
        T_IS_EQUAL,                 // ==
        T_IS_NOT_EQUAL,             // != or <>
        T_IS_SMALLER_OR_EQUAL,      // <=
        T_IS_GREATER_OR_EQUAL,      // >=
        T_INC,                      // ++
        T_DEC,                      // --
        T_PLUS_EQUAL,               // +=
        T_MINUS_EQUAL,              // -=
        T_MUL_EQUAL,                // *=
        T_DIV_EQUAL,                // /=
        T_IS_IDENTICAL,             // ===
        T_IS_NOT_IDENTICAL,         // !==
        T_DOUBLE_COLON,             // ::
        T_PAAMAYIM_NEKUDOTAYIM,     // ::
        T_OBJECT_OPERATOR,          // ->
        T_DOLLAR_OPEN_CURLY_BRACES, // ${
        T_AND_EQUAL,                // &=
        T_MOD_EQUAL,                // %=
        T_XOR_EQUAL,                // ^=
        T_OR_EQUAL,                 // |=
        T_SL,                       // <<
        T_SR,                       // >>
        T_SL_EQUAL,                 // <<=
        T_SR_EQUAL,                 // >>=
    );
    if(is_file($src)) {
        if(!$src = file_get_contents($src)) {
            return false;
        }
    }
    $tokens = token_get_all($src);
    
    $new = "";
    $c = sizeof($tokens);
    $iw = false; // ignore whitespace
    $ih = false; // in HEREDOC
    $ls = "";    // last sign
    $ot = null;  // open tag
    for($i = 0; $i < $c; $i++) {
        $token = $tokens[$i];
        if(is_array($token)) {
            list($tn, $ts) = $token; // tokens: number, string, line
            $tname = token_name($tn);
            if($tn == T_INLINE_HTML) {
                $new .= $ts;
                $iw = false;
            } else {
                if($tn == T_OPEN_TAG) {
                    if(strpos($ts, " ") || strpos($ts, "\n") || strpos($ts, "\t") || strpos($ts, "\r")) {
                        $ts = rtrim($ts);
                    }
                    $ts .= " ";
                    $new .= $ts;
                    $ot = T_OPEN_TAG;
                    $iw = true;
                } elseif($tn == T_OPEN_TAG_WITH_ECHO) {
                    $new .= $ts;
                    $ot = T_OPEN_TAG_WITH_ECHO;
                    $iw = true;
                } elseif($tn == T_CLOSE_TAG) {
                    if($ot == T_OPEN_TAG_WITH_ECHO) {
                        $new = rtrim($new, "; ");
                    } else {
                        $ts = " ".$ts;
                    }
                    $new .= $ts;
                    $ot = null;
                    $iw = false;
                } elseif(in_array($tn, $IW)) {
                    $new .= $ts;
                    $iw = true;
                } elseif($tn == T_CONSTANT_ENCAPSED_STRING
                       || $tn == T_ENCAPSED_AND_WHITESPACE)
                {
                    if($ts[0] == '"') {
                        $ts = addcslashes($ts, "\n\t\r");
                    }
                    $new .= $ts;
                    $iw = true;
                } elseif($tn == T_WHITESPACE) {
                    $nt = @$tokens[$i+1];
                    if(!$iw && (!is_string($nt) || $nt == '$') && !in_array($nt[0], $IW)) {
                        $new .= " ";
                    }
                    $iw = false;
                } elseif($tn == T_START_HEREDOC) {
                    $new .= "<<<S\n";
                    $iw = false;
                    $ih = true; // in HEREDOC
                } elseif($tn == T_END_HEREDOC) {
                    $new .= "S;";
                    $iw = true;
                    $ih = false; // in HEREDOC
                    for($j = $i+1; $j < $c; $j++) {
                        if(is_string($tokens[$j]) && $tokens[$j] == ";") {
                            $i = $j;
                            break;
                        } else if($tokens[$j][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                } elseif($tn == T_COMMENT || $tn == T_DOC_COMMENT) {
                    $iw = true;
                } else {
                    /*if(!$ih) {
                        $ts = strtolower($ts);
                    }küçültme olmayacak*/
                    $new .= $ts;
                    $iw = false;
                }
            }
            $ls = "";
        } else {
            if(($token != ";" && $token != ":") || $ls != $token) {
                $new .= $token;
                $ls = $token;
            }
            $iw = true;
        }
    }
    return $new;
}
