<?php
/**
 * Abstract class for models.
 */
Abstract Class Model {

    /**
     * @registry object
     */
    protected $registry;

    /**
     *
     */
    public $count1;

    /**
     * Constructor.
     */
    function __construct() {
        global $registry;
        $this->registry = $registry;
    }

    /**
     * I know for a fact that PDO::lastInsertId does not work with MSSQL and
     * PostgreSQL, so I wrapped the function in here for you to create
     * workarounds based on the different PDO drivers.
     */
    function getLastInsertId($column='id') {
        return $this->registry->db->lastInsertId($column);
    }

    /*** From here on we are implementing a better way to query the DB ***/

    private $select = '*';
    private $order = '';
    private $where = '';
    private $limit = '';
    private $where_params = array();

    public function to_sql() {
        $query = "SELECT {$this->select} FROM {$this->table()}";
        if (!empty($this->where)) {
            $query.= " WHERE ".$this->where;
        }
        if (!empty($this->order)) {
            $query.= " ORDER BY ".$this->order;
        }
        if (!empty($this->limit)) {
            $query.= " LIMIT ".$this->limit;
        }

        return $query;
    }

    public function sql_clear() {
        $this->select = '*';
        $this->order = '';
        $this->where = '';
        $this->limit = '';
        $this->where_params = array();
    }

    public function all() {
        $stmt = $this->registry->db->prepare($this->to_sql());

        if (!empty($this->where_params)) {
            foreach ($this->where_params as $key => $param) {
                $stmt->bindValue($key+1, $param);
            }
        }

        $result = $stmt->execute();

        $set = array();
        if ($result) {
            $set = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
        } else {
            $error_info = $stmt->errorInfo();
            throw new Exception($error_info[2]);
        }

        # Clear the query.
        $this->sql_clear();

        return $set;
    }

    /**
     * Returns the first occurrence.
     */
    public function first() {
        $model = $this->limit(1)->all();
        if (empty($model)) {
            return null;
        } else {
            return $model[0];
        }
    }

    /**
     * Columns to select.
     */
    public function select($columns) {
        $this->select = $columns;
        return $this;
    }

    /**
     * Where clause.
     *
     * Chained where() methods are binded by AND
     *
     * ie:
     *      where('col1 = ?', 1)->where('col2 = ?', 2);
     * outputs
     *      col1 = 1 AND col2 = 2
     *
     * You can send an array of elements for an IN clause like this:
     *
     *      where('id IN (:array:)', array(1,2,3))
     *
     * Or use a Model like this:
     *
     *      where('id IN (:idSection:)', $array_of_sections)
     *
     */
    public function where() {
        $arg_list = func_get_args();
        $clause = $arg_list[0];

        for ($i=1; $i < count($arg_list); $i++) {
            if (is_array($arg_list[$i])) {
                $pos1 = strpos($clause, ':');
                if ($pos1 === false) {
                    continue;
                }

                $pos2 = strpos($clause, ':', $pos1+1);
                if ($pos2 === false) {
                    continue;
                }

                $param = substr($clause, $pos1+1, $pos2-$pos1-1);
                $question_marks = '';
                if (empty($arg_list[$i])) {
                    $question_marks = '?,';
                    $this->where_params[] = -1;
                } elseif ($arg_list[$i][0] instanceof Model) {
                    foreach ($arg_list[$i] as $model) {
                        $this->where_params[] = $model->$param;
                        $question_marks.= '?,';
                    }
                } else {
                    foreach ($arg_list[$i] as $item) {
                        $this->where_params[] = $item;
                        $question_marks.= '?,';
                    }
                }
                $question_marks = substr($question_marks, 0, strlen($question_marks)-1);
                $clause = substr_replace($clause, $question_marks, $pos1, $pos2-$pos1+1);
            } else {
                $this->where_params[] = $arg_list[$i];
            }
        }

        if (empty($this->where)) {
            $this->where = $clause;
        } else {
            $this->where.= ' AND '.$clause;
        }


        return $this;
    }

    /**
     * Order by.
     */
    public function order($by) {
        $this->order = $by;
        return $this;
    }

    /**
     * Limits the records to return.
     */
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Returns the table name.
     *
     * Overwrite this method if you believe the table name is wrong.
     */
    protected function table() {
        return get_class($this).'s';
    }

}
