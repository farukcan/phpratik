<?php
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
			
			$__rotaAyar = array();

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
					else $sayfa.="<?php".$data.'?>'; // php kısmı
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
						$__rotaAyar[$s] = $app->loadAyar($s); // PERFORMSN AÇISINDAN SIKINTILI
					if($api[$s]->cache){ // yüklenecek apinin cachesi varsa
						require_once '/sistem/api/'.$s.'.php'; // classını yükle
						$return=$app->cache($api[$s],$sayfa); // app, cache işleme fonksiyonuna gönder
						$sayfa = $return[0]; // sayfanın yenideğeri dizin ilk değeri
						if($return[1]) // bir değişiklik yapıldı mı ?
							$apicache=true; 
					}

				}//foreach sonu
				

				
					
			}// while sonu


			
			//echo '<h1>Eklenecek Apiler</h1>';
			//var_dump($addapi);
			// geri birleştirme ?

			// Classları ve ayarlarını sayfa başına ekle __rotaAyar


			

			foreach (array_keys($addapi) as $apiAd) {
				if(!$api[$apiAd]->cacheonly)
					$sayfa=okuDosya('sistem/api/'.$apiAd.'.php')."?>".$sayfa;
			}

			$sayfa= '<?php $__rotaAyar='.var_export($__rotaAyar,true).';?>'.$sayfa;

			

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
