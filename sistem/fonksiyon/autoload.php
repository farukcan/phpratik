<?php
	/*
	|--------------------------------------------------------------------------
	| autoload 
	|--------------------------------------------------------------------------
	| bu dosya , sisteme sadece gerekli şeyleri dahil etmek için vardır.
	| classları apileri modelleri vb. şeyleri birleştirerek tek dosyaya derler. Bu özellik bize performans sağlar
	| amacımız gereksiz kodları ve kod tekrarını 0 a indirgemektir
	| Buradaki amaçlarımız
	| dosya değişimlerini tesbit
	| cache dosyaları güncelleme
	| class model kontrol sayfa bileşimini sağlama
	*/
	



	require 'sistem/fonksiyon/filetimes.php';

	if($app->fs_changed) {

		require_once 'sistem/fonksiyon/caching.php';
		echo '<h1>ROTALAR YENIDEN OLUSTURULDU</h1>';

		if(is_file($app->rotaFile)) //buraişlemler için sorun olabilir
			yonlendir($_GET['htaccess_url']);
		

	}

	if(is_file($app->rotaFile)) require $app->rotaFile;
	else require_once 'sistem/fonksiyon/404.php';




	