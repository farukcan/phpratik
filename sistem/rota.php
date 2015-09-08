<?php
return array(
	"rotalar" => array(

		array(
				"url" => "test{sayfa}",
				"kontrol" => "mainKontrol",
				"get" => array(),
				"post" => array(),
				"validasyon" => array()
			),

		array(
				"url" => "anasayfa{sayfa}-{sayfa2}",
				"kontrol" => "anaKontrol",
				"get" => array(),
				"post" => array(),
				"validasyon" => array("H@csrf")
			),


		)
);