<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * MVC - Library untuk framework kecik, library dibuat khusus untuk membantu masalah MVC
 *
 * @author 		Dony Wahyu Isp
 * @copyright 	2015 Dony Wahyu Isp
 * @link 		http://github.com/kecik-framework/session
 * @license		MIT
 * @version 	1.0.2
 * @package		Kecik\Session
 **/
namespace Kecik;

/**
 * Session
 * @package 	Kecik\Session
 * @author 		Dony Wahyu Isp
 * @since 		1.0.0-alpha
 **/
class MVC {
	static $db;

	public function __construct(Database $db) {
		self::$db = $db;
	}

	public static function setDB(Database $db) {
		self::$db = $db;
	}
}

require_once('Controller.php');
require_once('Model.php');