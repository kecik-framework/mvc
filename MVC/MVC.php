<?php

class MVC {
	static $db;

	public function __construct($db) {
		self::$db = $db;
	}

}