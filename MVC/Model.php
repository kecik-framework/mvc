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
 * @link 		http://github.com/kecik-framework/session
 * @license		MIT
 * @version 	1.0.1-alpha
 * @package		Kecik\Model
 **/
namespace Kecik;

/**
 * Model
 * @package 	Kecik\Model
 * @author 		Dony Wahyu Isp
 * @since 		1.0.0-alpha
 **/
class Model {
	protected static $db = NULL;
	protected static $table = '';
	protected static $_id;
	protected static $_data = [];
	protected $add = TRUE;
	protected $is_instance = FALSE;

	/**
	 * save
	 * Fungsi untuk menambah atau mengupdate record (Insert/Update)
	 * @return string SQL Query
	 **/
	public function save() {

		if (static::$table != '') {
			$table = static::$table;
			// Untuk menambah record
			if ($this->add == TRUE)
				$ret = self::$db->$table->insert(self::$_data);
			// Untuk mengupdate record
			else
				$ret = self::$db->$table->update(self::$_id, self::$_data);
			

			//silakan tambah code database sendiri disini


			//-- Akhir tambah code database sendiri
			self::$_data = [];
		}

		
		return $ret;
	}

	/**
	 * delete
	 * Fungsi untuk menghapus record
	 * @return string SQL Query
	 **/
	public function delete() {

		if (static::$table != '') {
			if (self::$_id != '' || (is_array(self::$_id) && count(self::$_id) > 0) ) {
				$table = static::$table;
				$ret = self::$db->$table->delete(self::$_id);
				self::$_data = [];
			} else
				$ret = FALSE;

			//silakan tambah code database sendiri disini


			//-- AKhir tambah code database sendiri
		}

		return $ret;
	}

	public static function find($condition=[], $limit=[], $order_by=[]) {
		self::$db = MVC::$db;

		$table = static::$table;
		return self::$db->$table->find($condition, $limit, $order_by);
	}

	//Silakan tambah fungsi model sendiri disini


	//-- Akhir tambah fungsi sendiri


	/**
	 * Model Constructor
	 * @param mixed $id
	 **/
	public function __construct($id='') {
		self::$db = MVC::$db;

		if ($id != '') {
			if (is_array($id))
				self::$_id = $id;
			else 
				self::$_id['id'] = $id;

			$this->add = FALSE;

			//Silakan tambah inisialisasi model sendiri disini

			//-- Akhir tambah inisialisasi model sendiri
		}

		$this->is_instance = TRUE;
	}

	public function __set($field, $value) {
		self::$_data[$field] = addslashes($value);
	}

	public function __get($field) {
		return stripslashes(self::$_data[$field]);
	}
} 