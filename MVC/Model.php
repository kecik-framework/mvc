<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * MVC Model
 *
 * @author 		Dony Wahyu Isp
 * @copyright 	2015 Dony Wahyu Isp
 * @link 		http://github.com/kecik-framework/mvc
 * @license		MIT
 * @version 	1.0.2
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
	protected static $pk = null;
	protected static $_id;
	protected static $_data = array();
	protected $add = TRUE;
	protected $is_instance = FALSE;
	protected $insert_id = null;

	/**
	 * save
	 * Fungsi untuk menambah atau mengupdate record (Insert/Update)
	 * @return string SQL Query
	 **/
	public function save() {

		if (empty(static::$table))
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;

		if ($table != '') {
			// Untuk menambah record
			if ($this->add == TRUE) {
				$before = $this->before();
				if (is_array($before) && count($before) > 1) {
					if (isset($before['insert'])) {
						$insert = $before['insert'];
						while(list($field, $value) = each($insert))
							self::$_data[$field] = $value;
					}
				}
				$ret = self::$db->$table->insert(self::$_data);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['insert']) && is_callable($after['insert'])) {
						$insert = $after['insert'];
						$insert(self::$_data);
					}
				}
			}
			// Untuk mengupdate record
			else {
				$before = $this->before();
				if (is_array($before) && count($before) > 1) {
					if (isset($before['update'])) {
						$update = $before['update'];
						while(list($field, $value) = each($update))
							self::$_data[$field] = $value;
					}
				}
				$ret = self::$db->$table->update(self::$_id, self::$_data);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['update']) && is_callable($after['update'])) {
						$update = $after['update'];
						$update(array('pk'=>self::$_id, 'data'=>self::$_data));
					}
				}
			}
			
			self::$_data = array();
		}

		
		return $ret;
	}

	/**
	 * delete
	 * Fungsi untuk menghapus record
	 * @return string SQL Query
	 **/
	public function delete() {

		if (empty(static::$table))
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;

		if ($table != '') {
			if (self::$_id != '' || (is_array(self::$_id) && count(self::$_id) > 0) ) {
				$before = $this->before();
				if (is_array($before) && count($before) > 1) {
					if (isset($before['delete'])) {
						$delete = $before['delete'];
						while(list($field, $value) = each($delete))
							self::$_id[$field] = $value;
					}
				}
				$ret = self::$db->$table->delete(self::$_id);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['delete']) && is_callable($after['delete'])) {
						$delete = $after['delete'];
						$delete(self::$_id);
					}
				}
				self::$_data = array();
			} else
				$ret = FALSE;
		}

		return $ret;
	}

	private static function __join($model, &$condition) {
		$modeljoin = '\Model\\'.$model;
		if (empty($modeljoin::$table)) {
			$table = strtolower($model);
		} else
			$table = $modeljoin::$table;

		if (is_array($modeljoin::relational()) && count($modeljoin::relational()) > 0) {
			$relational = $modeljoin::relational();
			if (isset($relational[0]) && !is_array($relational[0])) {
				$model = '\Model\\'.$relational[0];

				if ($table == $model::$table || empty($model::$table)) 
					$join_table = strtolower($relational[0]);
				else
					$join_table = $model::$table;

				if (strpos($relational[1], '.') === false)
					$relational[1] = "$join_table.$relational[1]";
				
				if (strpos($relational[2], '.') == false)
					$relational[2] = "$table.$relational[2]";

				$condition['join'][] = ['left', $join_table, [$relational[1], $relational[2]]];

				$modeljoin = $relational[0];
				self::__join($modeljoin, $condition);
			} else {
				while(list($id, $relation) = each($relational) ) {
					$model = '\Model\\'.$relation[0];
					
					if ($table == $model::$table || empty($model::$table)) 
						$join_table = strtolower($relation[0]);
					else
						$join_table = $model::$table;

					if (strpos($relation[1], '.') === false)
						$relation[1] = "$join_table.$relation[1]";
					
					if (strpos($relational[2], '.') == false)
						$relation[2] = "$table.$relation[2]";

					$condition['join'][] = ['left', $join_table, [$relation[1], $relation[2]]];
				}

				reset($relational);
				while (list($id, $relation) = each($relational)) {
					$modeljoin = $relation[0];
					self::__join($modeljoin, $condition);
				}
			}
		}
	}

	/**
	 * find
	 * function for select query
	 * @param Condition ['select', 'where', 'join']
	 * @param Limit [limit] or [offset, limit]
	 * @param Order By ['asc'=>['field1', 'field2'], 'desc'=>['field3']]
	 * @return array rows
	 **/
	public static function find($condition=array(), $limit=array(), $order_by=array()) {
		self::$db = MVC::$db;

		if (empty(static::$table)) 
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;

		
		if (is_array(static::relational()) && count(static::relational()) > 0) {
			$relational = static::relational();
			if (isset($relational[0]) && !is_array($relational[0])) {
				$model = '\Model\\'.$relational[0];

				if ($table == $model::$table || empty($model::$table)) 
					$join_table = strtolower($relational[0]);
				else
					$join_table = $model::$table;

				if (count($relational) == 1)
					$condition['join'][] = ['natural', $join_table];
				elseif (count($relational) == 2)
					$condition['join'][] = ['left', $join_table, $relational[1]];
				elseif (count($relational) == 3)
					$condition['join'][] = ['left', $join_table, [$relational[1], $relational[2]]];

				$modeljoin = $relational[0];
				self::__join($modeljoin, $condition);
			} else {
				while(list($id, $relation) = each($relational) ) {
					$model = '\Model\\'.$relation[0];
					
					if ($table == $model::$table || empty($model::$table)) 
						$join_table = strtolower($relation[0]);
					else
						$join_table = $model::$table;

					if (count($relation) == 1)
						$condition['join'][] = ['natural', $join_table];
					elseif (count($relation) == 2)
						$condition['join'][] = ['left', $join_table, $relation[1]];
					elseif (count($relation) == 3)
						$condition['join'][] = ['left', $join_table, [$relation[1], $relation[2]]];
				}

				reset($relational);
				while (list($id, $relation) = each($relational)) {
					$modeljoin = $relation[0];
					self::__join($modeljoin, $condition);
				}
			}
		}
		

		if (!isset($condition['callback']) && is_array(static::callback()) && count(static::callback()))
			$condition['callback'] = static::callback();

		$rows = self::$db->$table->find($condition, $limit, $order_by);
		return $rows;
	}

	public static function fields() {
		self::$db = MVC::$db;

		if (empty(static::$table))
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;

		return self::$db->$table->fields();
	}

	public static function num_rows() {
		return self::$db->num_rows();
	}

	/**
	 * relational()
	 * Overide for relational
	 * @return array []
	 **/
	public static function relational() {
		return array();
	}

	/**
	 * callback()
	 * Overide for callback
	 * @return array []
	 **/
	public static function callback() {
        return FALSE;
    }

    public function insert_id($field_id='') {
    	return $this->insert_id($field_id);
    }

	/**
	 * call static findFieldOperator
	 * findName("'name'") or findNameNot("'name'") or findNameLike("'%name%'") or findNameNotLike("'%name%'") or
	 * findProgressBetween([80, 100]) or findProgressNotBetween([80, 100]) or findProgressIn([70, 80, 100]) or
	 * findProgressNotIn([70, 80, 100])
	 * @return FALSE or array rows
	 **/
	public static function __callStatic($name, $args) {
		if (substr($name, 0, strlen('find')) == 'find') {
			$name  = substr($name, strlen('find'));
			if ($name[0] == strtoupper($name[0])) {
				$optname = '';
				if (substr($name, -strlen('like')) == 'Like') {
					$optname = 'Like';
					$name = substr($name, 0, -strlen('like'));
				} elseif (substr($name, -\strlen('between')) == 'Between') {
					$optname = 'Between';
					$name = substr($name, 0, -strlen('between'));
				} elseif (substr($name, -\strlen('in')) == 'In') {
					$optname = 'In';
					$name = substr($name, 0, -strlen('in'));
				} else {
					$optname = '=';
					$field = $name;
				}

				if (substr($name, -strlen('not')) == 'Not') {
					$optname = ($optname == '=')? '<>':'Not '.$optname;
					$name = substr($name, 0, -strlen('not'));
				} 

				$name = ($name != strtoupper($name))? strtolower($name):$name;

				$condition = [
					'where' => [
						[$name, $optname, $args[0]]
					]
				];

				if (!isset($args[1])) $args[1] = array();
				if (!isset($args[2])) $args[2] = array();
				return self::find($condition, $args[1], $args[2]);
			}

			return FALSE;
		}
	}

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

		}

		$this->is_instance = TRUE;
	}

	public function __set($field, $value) {
		if (is_array($value)) {
			self::$_data[$field] = $value;
			$this->$field = $value;
		} else {
			self::$_data[$field] = addslashes($value);
			$this->$field = addslashes($value);
		}
	}

	public function __get($field) {
		if (isset($this->$field))
			return stripslashes(self::$_data[$field]);
		else
			return null;
	}
} 