<?php
/*
|--------------------------------------------------------------------------
| h
|--------------------------------------------------------------------------
| HTML kodlarını kolay üretmeye yarar
| 
*/

class H {
    // bunu düzenle
    /*$tablo = array(
        array(5,6,8,9,6),
        array(5,6,8,9,6),
        array(5,6,8,9,6),
        array(5,6,8,9,6)
    );
    $TABLO = arr2table($tablo);*/
    static function arr2table($arr,$att=false){
        $html = "";
            foreach($arr as $sat){
                $html.="<tr>";
                foreach($sat as $sut){
                    $html.="<td>$sut</td>";
                }
                $html.="<tr>";
            }
        return xml("table",$html,$att);
    }

    static function xml($tag,$data=false,$att=false)
    {
        // <tag atts> data </tag>

        $at = "";
        if($att){
            foreach ($att as $key => $value) {
                $at.=" ".$key.'="'.$value.'"';
            }
        }

        if($data) return "<$tag$at>$data</$tag>";
        return "<$tag$att/>";
    }

    static function FormOpen($att=false,$csrf=true)
    {
        $at = "";
        if($att){
            foreach ($att as $key => $value) {
                $at.=" ".$key.'="'.$value.'"';
            }
        }
        if($csrf)
          return "<FORM$at>";
        else
          return "<FORM$at>";
    }

    static function FormClose()
    {
        return "</FORM>";
    }

    static function csrf(){
        
    }
}
