<htm kodları>

<?php



//var_dump(Vt::sorgu()->tablo('oturum')->hepsiniAl());

/*
Vt::sor()->tablo('oturum')->ekle([
	"kod" => "denedsme",
	"veri" => "deneme bilgi"
]);*/


Vt::sorgu()->tablo('oturum')->nerede([ "kod" => "deneme"])->sil();

Oturum::set("elme","armut".uniqid());
var_dump(Oturum::get("elme"));

//Mcache::set("ben","sen");

$d = Mcache::get("ben");
var_dump($d);

echo "<hr>";


Model::kullan();



//$kelime = kelimeler::hepsiniBul("lojik = 13"); // read category into a new object
//$kelime->lojik = "13";
//$kelime->save();
//var_dump($kelime);



var_dump(Model::getTables());



$ali = array('ahmet','mehmet','süleyman');

echo H::xml("a",'Tıkla',array('href'=>''));
Sayfa::yap('altsayfa@ustsayfa@KALIP');
Girdi::hepsiniAl();
//echo 'id : "'.$id .'" değeri alındı';
Sayfa::yap('ornek2');

//Model::yap();
//Girdi::x;
//var_dump(Vt::pdox()->select()->from('kelimeler') );
echo isset($ahmet) ? $ahmet : '<br>naber';
?>
ss
gene html
s
<?php
	//php kodları
