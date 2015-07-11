<htm kodları>

<?php

$ali = array('ahmet','mehmet','süleyman');

echo H::xml("a",'Tıkla',array('href'=>''));
Sayfa::yap('altsayfa@ustsayfa@KALIP');
Girdi::hepsiniAl();
//echo 'id : "'.$id .'" değeri alındı';
Sayfa::yap('ornek2');

Model::kullan();


class kelimeler extends Model{
	static protected $_tableName = 'kelimeler';
}
 $category = kelimeler::getById(1002); // read category into a new object
var_dump($category);



var_dump(Model::getTables());
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
