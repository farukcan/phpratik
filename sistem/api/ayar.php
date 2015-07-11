<?php

class Ayar {
	static function al($api="ayar",$ayar=false)
	{
		if($ayar===false)
			return $GLOBALS['__rotaAyar'][$api];
		else
			return $GLOBALS['__rotaAyar'][$api][$ayar];
	}
}