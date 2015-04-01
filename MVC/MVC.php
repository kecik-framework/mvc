<?php

class MVC {
	static $db;

	public function __construct(Database $db) {
		self::$db = $db;
	}

}