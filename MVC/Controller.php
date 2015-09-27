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
	protected $request = '';
	protected $url = '';
	protected $assets = '';
	protected $config = '';
	protected $route = '';
	//protected $container = '';
	//protected $db = '';

	/**
	 * Construtor Controller
	 **/
	public function __construct() {
		//Silakan tambah inisialisasi controller sendiri disini

		//-- Akhir tambah inisialisasi sendiri

		$app = Kecik::getInstance();
		$this->request = $app->request;
		$this->url = $app->url;
		$this->assets = $app->assets;
		$this->config = $app->config;
		$this->route = $app->route;

		$libraries = $app->getLibrariesEnabled();
		while(list($idx, $library) = each($libraries)) {
			$lib = $library[0];
			if (isset($app->$lib))
			$this->$Lib = $app->$lib;
		}
			
		/*if (isset($app->container))
			$this->container = $app->container;
		if (isset($app->db))
			$this->db = $app->db;
		if (isset($app->session))
			$this->session = $app->session;
		if (isset($app->cookie))
			$this->cookie = $app->cookie;
		if (isset($app->language))
			$this->language = $app->language;*/
	}

	//Silakan tambah fungsi controller sendiri disini


	//-- Akhir tambah fungsi sendiri

	/**
	 * view
	 * Funngsi untuk menampilkan view
	 * @param string $file
	 * @param array $param
	 **/
	protected function view($file, $param=array()) {
		extract($param);
		
		if (!is_array($file)) {
			$path =  explode('\\', get_class($this));

			if (count($path) > 2) {
				$view_path = '';

				for($i=0; $i<count($path)-2; $i++) {
					$view_path .= strtolower($path[$i]).'/';
				}	

				$view_path = Config::get('path.mvc').'/'.$view_path;
			} else {
				$view_path = Config::get('path.mvc');
			}

			if (php_sapi_name() == 'cli')
				$view_path = Config::get('path.basepath').'/'.$view_path;
		} else {
			
			$view_path = Config::get('path.mvc');
			if (isset($file[1])) {
				$view_path .= '/'.$file[0];
				$file = $file[1];
			} else
				$file = $file[0];

			if (php_sapi_name() == 'cli')
				$view_path = Config::get('path.basepath').'/'.$view_path; 
			
		}
		
		include $view_path.'/views/'.$file.'.php';
	}
}