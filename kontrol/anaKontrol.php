<?php
//echo $sayfa . "<br>" . $sayfa2;
echo "hey";

echo Ayar::al();



$vt = Vt::pdox();

$a=$vt->select()->from('kelimeler')->getAll();;


var_dump($a);



