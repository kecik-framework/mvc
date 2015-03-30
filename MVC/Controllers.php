<?php

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
	protected function view($file, $param=array()) {
		extract($param);
		include Config::get('path.mvc').'/views/'.$file.'.php';
	}
}