<htm kodları>

<?php

Mcache::set("ben","sen");

$d = Mcache::delete("ben");
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
