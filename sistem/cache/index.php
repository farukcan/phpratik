<?php
// burada sadece define'ler bulunur. özel ayarlar için; ayar.php yi kullanın ve Ayar::al('ayar','deger') ile elde edin.
define('ROOT', str_replace(array('index.php','Index.php'), '', $_SERVER['SCRIPT_FILENAME'])); //'C:\wamp\www\adynt2015/'
define('developer_mode', true);

$_degiskensiz=array (
);$_degiskenli=array (
  0 => 'anasayfa{sayfa}-{sayfa2}',
  1 => 'test{sayfa}',
);