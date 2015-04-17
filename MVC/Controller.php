<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * Session - Library untuk framework kecik, library ini khusus untuk membantu masalah session 
 *
 * @author 		Dony Wahyu Isp
 * @copyright 	2015 Dony Wahyu Isp
 * @link 		http://github.com/kecik-framework/mvc
 * @license		MIT
 * @version 	1.0.2-alpha
 * @package		Kecik\Controller
 **/
namespace Kecik;

/**
 * Controller
 * @package 	Kecik\Controller
 * @author 		Dony Wahyu Isp
 * @since 		1.0.0-alpha
 **/
class Controller {

	/**
	 * Construtor Controller
	 **/
	public function __construct() {
		//Silakan tambah inisialisasi controller sendiri disini

		//-- Akhir tambah inisialisasi sendiri
	}

	//Silakan tambah fungsi controller sendiri disini


	//-- Akhir tambah fungsi sendiri

	/**
	 * view
	 * Funngsi untuk menampilkan view
	 * @param string $file
	 * @param array $param
	 **/
	protected function view($file, $param=[]) {
		ob_start();
		extract($param);
		$myfile = fopen(Config::get('path.mvc').'/views/'.$file.'.php', "r");
		$view = fread($myfile,filesize(Config::get('path.mvc').'/views/'.$file.'.php'));
		fclose($myfile);
		//$view = file_get_contents( Config::get('path.mvc').'/views/'.$file.'.php' );
		eval('?>'.$view);
		$result = ob_get_clean();
		
		return $result;
	}
}