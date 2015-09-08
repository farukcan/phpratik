<?php
return array(
		"motor" => "dosya", // dosya olarak kaydeder : "vt" :: veri tabanına kaydeder "mcache" :: mcacheye kaydeder
		"zaman" => 60*60, // 1 saaat

		

		// Dosya için ayarlar

		"klasor" => "sistem/cache/oturum",
		

		
		/*

		
			// Veri Tabanı için ayarlar
			// önemliNOT: api/oturum.js 'dan gerekli apiler'e vt'yi ekleyin.

			"tablo" => "oturum",
			"kodSutunu" => "kod",
			"veriSutunu" => "veri",


		*/		

		/*
		
		 	// Mcache için ayarlar
		 	// önemliNOT: api/oturum.js 'dan gerekli apiler'e mcache'yi ekleyin

			"soyad_" => "oturum_", // oturum_das5d4a564 gibi değişken isimleriyle kaydedilecek.

		*/

		



		"yol" => null // null kalırsa kendi ayarlar, "/" şekliden değer verebilirsiniz.


	);

