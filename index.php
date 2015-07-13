<?php
define('sistem_start', microtime(true));
	/*
	|--------------------------------------------------------------------------
	| index.php
	|--------------------------------------------------------------------------
	| Bu dosya, sistemin çekirdeğidir. 
	| Burada değişim yapmanız tavsiye edilmez.
	*/


if(is_file('sistem/cache/index.php')){
	require_once 'sistem/cache/index.php'; // bütün işleri sadece 2 require_once ile hallederiz. 3 dosyaya erişmek yeter
	$app = new App;
}else{
	require_once 'sistem/ayar/genel.php';
	$app = new App;
	$app->fs_changed = true;	
}


if(is_file($app->rotaFile) && !developer_mode) // Dosya var ve geliştirme modunda değilse,
	require $app->rotaFile; // direk olarak işlenmiş rota çalıştırılır.
else	
	require 'sistem/fonksiyon/autoload.php'; // aksi taktirde, değişimler kontrol edilir.

echo '<h4>Gecikme </h4>';
var_dump ($app->runningTime()); // sistem çalışma hızı

//-- SON


function url_oku($istenen,$gelen)
{
	// { } taglarına ayır
	$istenen = str_replace(array('{','}'), '|', $istenen); // {} || şeklinde url içi değişkenleri auır
	$exploded = explode('|', $istenen);
	foreach ($exploded as $key => $value) { //tek olanlar değişken adlarıdır
		if($key%2===0) unset($exploded[$key]);
		else $istenen = str_replace("|$value|", '(.*)', $istenen);
	}
		

	$istenen = '/'.str_replace('/', '\/', $istenen).'/'; //pregmatch paterni haline getir
	if(preg_match($istenen, $gelen, $output_array)){
			$return = array(); //dataları bunla döndüreceğiz
			$ix = 0;
			foreach ($exploded as $value) {
				$ix++;
				$return[$value] = $output_array[$ix];
			}
			return $return;
	} //ayrıştır
	return false;
}

function yonlendir($url){
	$url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) .$url;
    if (!headers_sent()){  
        header('Location: '.$url); exit; 
    }else{ 
        $sayfaKod.= '<script type="text/javascript">'; 
        $sayfaKod.= 'window.location.href="'.$url.'";'; 
        $sayfaKod.= '</script>'; 
        $sayfaKod.= '<noscript>'; 
        $sayfaKod.= '<meta http-equiv="refresh" content="0;url='.$url.'" />'; 
        $sayfaKod.= '</noscript>'; exit; 
    }
}


/**
* APP sınıfı, uygulamanın ana sınıfıdır
* @author farukcan.net
* @version 555
*/
class App
{
	public $fs_changed = false; //dosya sistemindeki değişimleri kontrol eder
	public $rotaFile; // cache klasöründeki rota dosyasıdır
	public $rota; //rota bilgisidir
	public function runningTime(){
		return microtime(true)-sistem_start;
	}

	function App(){ // burada URL işlenerek rota belirlenmeli
		if(!isset($_GET['htaccess_url'])) $_GET['htaccess_url']='';
		// url'yi ilk değişkensizlerde ara (cacheden geliyo bunlar)	
		// bulamazsa oku_url ile değişkenlilerde ara
		// rota bulunduğunda yönlendir (varsa url-global değişkenler beraber)

			foreach ($GLOBALS['_degiskensiz'] as $_url){
				if($_url==$_GET['htaccess_url']){
					$this->rota = $_GET['htaccess_url'];
					$this->rotaFile = 'sistem/cache/rota/'.sha1($this->rota);
					return;				
				}	
			}

			//bulamazsa - değişkenlilerde arar
			foreach ($GLOBALS['_degiskenli'] as $_url) {
				$url_data=url_oku( $_url,$_GET['htaccess_url']);
				if($url_data===false){
					//uyuşmazsa geç
				}
				else
				{
					$this->rota = $_url;
					$this->rotaFile = 'sistem/cache/rota/'.sha1($this->rota);
					foreach ($url_data as $key => $value) 
						$GLOBALS[$key]=$value;
					return;		
				}
			}






	}

	function loadAyar($api){
		return require 'sistem/ayar/'.$api.'.php';
	}

	function loadRota(){
		return require 'sistem/rota.php';
	}
}


	/*
	|--------------------------------------------------------------------------
	|  Fonksiyonlar
	|--------------------------------------------------------------------------
	|
	| 
	*/
function a2c($array){ //array a[d] yerine a->d yapar
	$class = new stdClass();
	foreach ($array as $key => $value)
		$class->{$key} = $value;
	return $class;
}

function okuDosya($file){  // bir dosyayı okur text şeklinde döndürür
    $myfile = fopen(ROOT.$file, "r") or die("Dosya Okunamadi!");
    $txt= fread($myfile,filesize(ROOT.$file));
    fclose($myfile);
    return $txt;	
}

function yazDosya($file,$data)
{
	if(is_file(ROOT.$file)) unlink(ROOT.$file);
	$myfile = fopen(ROOT.$file, "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}

function getFilenames($dic,$ext,$addExt=false){
	$list=array(); 
	if($addExt){
		foreach (scandir($dic) as $key => $value)
			if(!is_bool(strpos($value, $ext))) array_push($list, $value) ;
	}else{	
		foreach (scandir($dic) as $key => $value)
			if(!is_bool(strpos($value, $ext))) array_push($list, str_replace($ext, '', $value)) ;
	}
	return $list;
}
function getAllFilenames($dic,$ext,$addExt=false,$subdic=''){
	$list=array(); 
	if($addExt){
		foreach (scandir($dic) as $key => $value)
			if(!is_bool(strpos($value, $ext))) array_push($list, $value) ;
			else if (is_dir($dic.'/'.$value) && $value!='.' && $value!='..') $list = array_merge($list,getAllFilenames($dic.'/'.$value,$ext,true,$subdic.$value.'/'));
	}else{	
		foreach (scandir($dic) as $key => $value)
			if(!is_bool(strpos($value, $ext))) array_push($list, str_replace($ext, '', $subdic.$value)) ;
			else if (is_dir($dic.'/'.$value) && $value!='.' && $value!='..') $list = array_merge($list,getAllFilenames($dic.'/'.$value,$ext,false,$subdic.$value.'/'));
	}
	return $list;
}

/**
* @example 
*echo "apiler";
*var_dump(getFilenames(ROOT.'/sistem/api','.php')); 
*
*echo "sayfalar";
*var_dump(getAllFilenames(ROOT.'/sayfa','.html')); 
*
*echo "kontroller";
*var_dump(getAllFilenames(ROOT.'/kontrol','.php')); 
*
*
*/

function getFiletimes($dic,$ext)
{
	$list=array(); //apilerin listesi
	foreach (scandir($dic) as $key => $value)
		if(!is_bool(strpos($value, $ext))) array_push($list, filemtime($dic.'/'.$value));

	return $list;
}
function getAllFiletimes($dic,$ext,$subdic=''){
	$list=array(); 
	foreach (scandir($dic) as $key => $value)
		if(!is_bool(strpos($value, $ext))) array_push($list, filemtime($dic.'/'.$value)) ;
		else if (is_dir($dic.'/'.$value) && $value!='.' && $value!='..') $list = array_merge($list,getAllFiletimes($dic.'/'.$value,$ext,$subdic.$value.'/'));
	return $list;
}

?>