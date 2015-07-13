<?php

class Ayar {
	static function al($api="ayar",$ayar=false)
	{
		if($ayar===false)
			return '$GLOBALS[\'__phpratikAyar\'][\''.$api.'\']';
		else
			return '$GLOBALS[\'__phpratikAyar\'][\''.$api.'\'][\''.$ayar.'\']';
	}

    function cache()
    {
        return array('al');
    }
}