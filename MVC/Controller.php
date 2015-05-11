<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * MVC Controller
 *
 * @author 		Dony Wahyu Isp
 * @copyright 	2015 Dony Wahyu Isp
 * @link 		http://github.com/kecik-framework/mvc
 * @license		MIT
 * @version 	1.0.2
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
	private $request = '';
	private $url = '';
	private $assets = '';
	private $config = '';
	//private $container = '';
	//private $db = '';

	/**
	 * Construtor Controller
	 **/
	public function __construct(Kecik $app) {
		//Silakan tambah inisialisasi controller sendiri disini

		//-- Akhir tambah inisialisasi sendiri

		$this->request = $app->request;
		$this->url = $app->url;
		$this->assets = $app->assets;
		$this->config = $app->config;
		if (isset($app->container))
			$this->container = $app->container;
		if (isset($app->db))
			$this->db = $app->db;
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
		if (php_sapi_name() == 'cli')
			$mvc_path = Config::get('path.basepath').Config::get('path.mvc');
		else
			$mvc_path = Config::get('path.mvc');

		/*ob_start();
		extract($param);
		$myfile = fopen(Config::get('path.mvc').'/views/'.$file.'.php', "r");
		$view = fread($myfile,filesize(Config::get('path.mvc').'/views/'.$file.'.php'));
		fclose($myfile);
		//$view = file_get_contents( Config::get('path.mvc').'/views/'.$file.'.php' );
		eval('?>'.$view);
		$result = ob_get_clean();
		*/
		extract($param);
		include $mvc_path.'/views/'.$file.'.php';
	}
}