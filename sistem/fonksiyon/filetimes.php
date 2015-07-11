<?php

$yeni_zamanlar = array(
		"kontrol" => getAllFiletimes(ROOT.'/kontrol','.php'),
		"sayfa" => getAllFiletimes(ROOT.'/sayfa','.html'),
		"api" => getFiletimes(ROOT.'/sistem/api','.php'),
		"apijson" => getFiletimes(ROOT.'/sistem/api','.json'),
		"ayar" => getFiletimes(ROOT.'/sistem/ayar','.php'),
		"dil" => getAllFiletimes(ROOT.'/sayfa','.php'),
		'rota' => filemtime(ROOT.'/sistem/rota.php')
	);


$eski_zamanlar = unserialize(okuDosya('sistem/cache/dosyalar.dat'));


if($eski_zamanlar!=$yeni_zamanlar) $app->fs_changed = true;







