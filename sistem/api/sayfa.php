<?php

/*
|--------------------------------------------------------------------------
| Sayfa
|--------------------------------------------------------------------------
| Html sayfaları işleyen ve aralarına veri dolduran sınıfdır
| DİKKAT : bu api silinemez bir parçadır!
*/


class Sayfa {
    static function doldur($sf,$ad,$alanlar=false)
    {
        // değiştireceğimiz ifadeler
        $degistirme = array(
                "/{{{/" => "<?php ",
                "/}}}/" => "; ?>",
                "/{{/" => "<?php echo ",
                "/}}/" => "; ?>",
                "/@if\((.*)\)/" => "<?php if($1){ ?>",
                "/@elseif\((.*)\)/" => "<?php }elseif($1){ ?>",
                "/@endif/" => "<?php } ?>",
                "/@else/" => "<?php }else{ ?>",
                "/@for\((.*)\)/" => "<?php for($1){ ?>",
                "/@foreach\((.*)\)/" => "<?php foreach($1){ ?>",
                "/@endforeach/" => "<?php } ?>",
                "/@endfor/" => "<?php } ?>", //$endforeachten sonra gelmeli
                "/@while\((.*)\)/" => "<?php while($1){ ?>",
                "/@endwhile/" => "<?php } ?>",
                "/@end/" => "<?php } ?>", // bu arrauın sonunda olmalı
                "/@include\((.*)\)/" => "<?php Sayfa::yap($1); ?>",
                "/@[Dd]ahil[Ee]t\((.*)\)/" => "<?php Sayfa::yap($1); ?>",
            );

        $html = preg_replace(array_keys($degistirme), array_values($degistirme), $sf);

        if(is_array($alanlar)){ //array ise alttaki parçaları burada birşeitiriz
            //@alan($key)'i $value ile değiş
            foreach ($alanlar as $key => $value) {
                $html = str_replace("@alan($key)", $value, $html);
            }
        }

        preg_match('/'.$GLOBALS['__phpratikAyar']['sayfa']['ayrac'].'(.*)/',  $ad, $extend); //üst sayfanın adı
        
        if(!empty($extend)){ // ext[1] ad

            if($alanlar==false) //alan değişkenini arry olrk hzrlylm
              $alanlar = array();

            //@parçabaşı-sonu arası değerleri alanlar içine alalım
            $istenen = '/@[Pp]arça[Bb]aşı\((.*)\)(.*)@[Pp]arça[Ss]onu/sU';
            preg_match_all($istenen, $html, $output_array);
            foreach ($output_array[1] as $key => $akey) 
                $alanlar[$akey] = $output_array[2][$key];

            //aynısı kısası olan pb-pş içinde yapalım
            $istenen = '/@pb\((.*)\)(.*)@ps/sU';
            preg_match_all($istenen, $html, $output_array);
            foreach ($output_array[1] as $key => $akey) 
                $alanlar[$akey] = $output_array[2][$key];

            //alanları bir üstte gönderelim
            return Sayfa::doldur(Sayfa::oku($extend[1]),$extend[1],$alanlar);
        }


        return $html; //geri döndür
    }
    static function oku($sayfa)
    {

        return okuDosya($GLOBALS['__phpratikAyar']['sayfa']['klasor'].$sayfa.'.html');
    }

    static function yap($sayfa) //bu cached fonksiyondur
    {
        return "?>".Sayfa::doldur(Sayfa::oku($sayfa),$sayfa)."<?php ";
    }

    static function cache()
    {
        //cache edilebilir fonksiyonlarımız
        return array('yap');
    }
}
