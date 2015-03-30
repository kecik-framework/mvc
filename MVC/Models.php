<?php

class Model {
	protected $_field = array();
	protected $_where;
	protected $add = TRUE;
	protected $table = '';
	protected $fields = array();
	protected $values = array();
	protected $updateVar = array();

	/**
	 * save
	 * Fungsi untuk menambah atau mengupdate record (Insert/Update)
	 * @return string SQL Query
	 **/
	public function save() {
		$this->setFieldsValues();

		if ($this->table != '') {
			// Untuk menambah record
			if ($this->add == TRUE) {
				$sql ="INSERT INTO `$this->table` ($this->fields) VALUES ($this->values)";
			// Untuk mengupdate record
			} else {
				$sql ="UPDATE `$this->table` SET $this->updateVar $this->_where";
			}

			//silakan tambah code database sendiri disini


			//-- Akhir tambah code database sendiri
		}

		return $this->db->exec( (isset($sql))?$sql:'' );
	}

	/**
	 * delete
	 * Fungsi untuk menghapus record
	 * @return string SQL Query
	 **/
	public function delete() {
		$this->setFieldsValues();

		if ($this->table != '') {
			if ($this->_where != '') {
				$sql = "DELETE FROM $this->table $this->_where";
			}

			//silakan tambah code database sendiri disini


			//-- AKhir tambah code database sendiri
		}

		return $this->db->exec( (isset($sql))?$sql:'' );
	}

	//Silakan tambah fungsi model sendiri disini


	//-- Akhir tambah fungsi sendiri


	/**
	 * Model Constructor
	 * @param mixed $id
	 **/
	public function __construct($id='') {
		$this->db = MVC::$db;

		$this->_where = '';
		if ($id != '') {
			if (is_array($id)) {
				$and = array();
				while(list($field, $value) = each($id)) {

					if (preg_match('/<|>|!=/', $value))
						$and[] = "`$field`$value";
					else
						$and[] = "`$field`='$value'";
				}
				$this->_where .= implode(' AND ', $and);
			} else {
				$this->_where .= "`id`='".$id."'";
			}

			$this->add = FALSE;

			//Silakan tambah inisialisasi model sendiri disini

			//-- Akhir tambah inisialisasi model sendiri
		}
	}

	/**
	 * setFieldValues
	 * Fungsi untuk menyetting Variable Fields dan Values
	 **/
	private function setFieldsValues() {
		$fields = array_keys($this->_field);
		while(list($id, $field) = each($fields))
			$fields[$id] = "`$fields[$id]`";
		
		$this->fields = implode(',', $fields);

		$values = array_values($this->_field);
		$updateVar = array();
		while (list($id, $value) = each($values)){
			$values[$id] = "'$values[$id]'";
			$updateVar[] = "$fields[$id] = $values[$id]";
		}
		$this->values = implode(',', $values);
		$this->updateVar = implode(',', $updateVar);

		$this->_where = ($this->_where != '')?' WHERE '.$this->_where:'';
	}

	public function __set($var, $value) {
		$this->_field[$var] = addslashes($value);
	}

	public function __get($var) {
		return stripslashes($this->_field[$var]);
	}
} 