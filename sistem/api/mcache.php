<?php

	class Mcache{
		static function check()
		{
			if(isset($GLOBALS["__mcache"]))
				return true;
			else
			{
				$GLOBALS["__mcache"]  = new Memcache;
				$GLOBALS["__mcache"]->connect($GLOBALS['__phpratikAyar']['mcache']['host'], $GLOBALS['__phpratikAyar']['mcache']['port']) or die ("MemCached servera baglanilamiyor!");
				return false;
			}

		}

		static function getClass()
		{
			Mcache::check();
			return $GLOBALS["__mcache"];
		}

		static function set($a,$b,$c=null,$d=null){
			Mcache::check();
			return $GLOBALS["__mcache"]->set($a,$b,$c,$d);
		}

		static function get($a,$b=null){
			Mcache::check();
			return $GLOBALS["__mcache"]->get($a,$b);
		}

		static function delete($a,$b=null){
			Mcache::check();	
			return $GLOBALS["__mcache"]->delete($a,$b);
		}

	}