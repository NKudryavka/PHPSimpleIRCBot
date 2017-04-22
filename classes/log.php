<?php

class Log {
	private static $isDebug = false;

	public static function debug($str) {
		if(self::$isDebug) echo $str."\r\n";
	}

	public static function setDebug() {
		self::$isDebug = true;
	}
}