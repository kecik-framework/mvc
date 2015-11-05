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
	protected $_id;
	protected $_modelId;
	protected static $modelId = 0;
	protected static $pid = array();
	protected static $currentId = 0;
	protected $_data = array();
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

		if (count($this->_data[$table]) <= 0 ) {
			$post = $_POST;
			while(list($field, $value) = each($post)) {
				// Next if is Primary Keys
				if (array_key_exists($field, $this->_id))
					continue;
				
				$this->_data[$table][$field] = addslashes($value);
				$this->$field = $value;
			}
		}

		if ($table != '') {
			// Untuk menambah record
			if ($this->add == TRUE) {
				$before = $this->before();
				if (is_array($before) && count($before) > 1) {
					if (isset($before['insert'])) {
						$insert = $before['insert'];
						while(list($field, $value) = each($insert))
							$this->_data[$table][$field] = $value;
					}
				}
				$ret = self::$db->$table->insert($this->_data[$table]);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['insert']) && is_callable($after['insert'])) {
						$insert = $after['insert'];
						$insert($this->_data[$table]);
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
							$this->_data[$table][$field] = $value;
					}
				}
				$ret = self::$db->$table->update($this->_id, $this->_data[$table]);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['update']) && is_callable($after['update'])) {
						$update = $after['update'];
						$update(array('pk'=>$this->_id, 'data'=>$this->_data[$table]));
					}
				}
			}
			
			$this->_data[$table] = array();
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
			if ($this->_id != '' || (is_array($this->_id) && count($this->_id) > 0) ) {
				$before = $this->before();
				if (is_array($before) && count($before) > 1) {
					if (isset($before['delete'])) {
						$delete = $before['delete'];
						while(list($field, $value) = each($delete))
							$this->_id[$field] = $value;
					}
				}
				$ret = self::$db->$table->delete($this->_id);
				$after = $this->after();
				if (is_array($after) && count($after) > 1) {
					if (isset($after['delete']) && is_callable($after['delete'])) {
						$delete = $after['delete'];
						$delete($this->_id);
					}
				}
				$this->_data[$table] = array();
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

				if (!isset($relational[2])) $relational[2] = $relational[1];

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

					if (!isset($relation[2])) $relation[2] = $relation[1];

					if (strpos($relation[1], '.') === false)
						$relation[1] = "$join_table.$relation[1]";
					
					if (strpos($relation[2], '.') == false)
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
		
		if (!isset(self::$pid[self::$currentId])) {
			if (!isset($condition['callback']) && is_array(static::callback()) && count(static::callback()))
				$condition['callback'] = static::callback();
		}

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

    public function before() {
    	return array();
    }

    public function after() {
    	return array();
    }
    
    public function insert_id($field_id='') {
    	return $this->insert_id($field_id);
    }

    public static function raw_find($condition=array(), $limit=array(), $order_by=array()) {
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

		if (!isset(self::$pid[self::$currentId])) {
			if (!isset($condition['callback']) && is_array(static::callback()) && count(static::callback()))
				$condition['callback'] = static::callback();
		}
		
		return self::$db->$table->raw_find($condition, $limit, $order_by);
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
				$this->_id = $id;
			else 
				$this->_id['id'] = $id;

			$this->add = FALSE;

		}

		$this->is_instance = TRUE;

		self::$modelId++;
		self::$currentId = self::$modelId;
		self::$pid[self::$modelId] = $this->_id;
		$this->_modelId = self::$modelId;
	}

	public function __set($field, $value) {
		if (empty(static::$table))
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;

		if (is_array($value)) {
			$this->_data[$table][$field] = $value;
			$this->$field = $value;
		} else {
			$this->_data[$table][$field] = addslashes($value);
			$this->$field = addslashes($value);
		}
	}

	public function __get($field) {
		if (empty(static::$table))
			$table = strtolower(substr(static::class, strpos(static::class, '\\')+1));
		else
			$table = static::$table;
		
		if (isset($this->_data[$table][$field])) {
			if (!is_object($this->_data[$table][$field]))
				return stripslashes($this->_data[$table][$field]);
			else
				return $this->_data[$table][$field];
		} else {
			// Get for Update
			if ($this->add != TRUE) {
				$where = array();
				reset($this->_id);
				while(list($f, $val)=each($this->_id))
					array_push($where, array($f, '=', $val));
				
				self::$currentId = $this->_modelId;
				$rows = $this->find(array('where'=>$where), array(1));
				foreach ($rows as $row) {
					
					foreach($row as $field_name => $value)
						$this->_data[$table][$field_name] = $value;
				}
				
				if (isset($this->_data[$table][$field])) {
					if (!is_object($this->_data[$table][$field]))
						return stripslashes($this->_data[$table][$field]);
					else
						return $this->_data[$table][$field];
				}
			}	
			// End Get for Update
			return null;
		}
	}
} 