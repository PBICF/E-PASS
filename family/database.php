<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

class Database
{
    private $conn = null;
    private static $instance = null;

    // Query builder properties
    private $table         = null;
    private $selectFields  = ['*'];
    private $whereClauses  = [];
    private $likeClauses   = [];
    private $bindings      = [];
    private $limitValue    = null;
    private $offsetValue   = null;
    private $orderByClause = null;
    private $groupByClause = null;
    private $havingClause  = null;
    private $isUpdate      = false;
    private $updateData    = [];
    private $updateBindings = [];
    private $joinClauses = [];
    private $updateRaw = [];

    public function __construct()
    {
        $this->connect('oci:dbname=//10.0.240.77:1521/pbicf', 'pass', 'alrs');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function connect(string $dsn, string $username, string $password)
    {
        try {
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Connection failed : " . $e->getMessage());
        }

        return $this;
    }

    // -------------------------------------------------------------------------
    // Query Builder Methods (Fluent chain)
    // -------------------------------------------------------------------------

    /**
     * Set the table to query.
     * @param string $table
     * @return $this
     */
    public function from(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Alias for from().
     */
    public function table(string $table)
    {
        return $this->from($table);
    }

    /**
     * Select fields.
     * @param string|array $fields
     * @return $this
     */
    public function select($fields = '*')
    {
        if (is_array($fields)) {
            $this->selectFields = $fields;
        } else {
            $this->selectFields = explode(',', $fields);
            $this->selectFields = array_map('trim', $this->selectFields);
        }
        return $this;
    }

    /**
     * Add a WHERE condition.
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        // ── Handle IS NULL / IS NOT NULL without a placeholder ──
        if (in_array(strtoupper($operator), ['IS', 'IS NOT']) && $value === null) {
            $this->whereClauses[] = [$column, $operator, null, true];  // flag for raw null
            // Do NOT add to $this->bindings
            return $this;
        }

        $this->whereClauses[] = [$column, $operator, $value];
        $this->bindings[] = $value;
        return $this;
    }

    public function _where($column, $operator = null, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->whereClauses[] = [$column, $operator, $value];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add a LIKE condition.
     * @param string $column
     * @param string $value
     * @param string $position 'before', 'after', 'both' (default)
     * @return $this
     */
    public function like($column, $value, $position = 'both')
    {
        switch ($position) {
            case 'before':
                $value = "%$value";
                break;
            case 'after':
                $value = "$value%";
                break;
            case 'both':
            default:
                $value = "%$value%";
                break;
        }
        $this->likeClauses[] = [$column, $value];
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Set ORDER BY.
     * @param string $column
     * @param string $direction ASC|DESC
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderByClause = "$column $direction";
        return $this;
    }

    /**
     * Set GROUP BY.
     * @param string|array $columns
     * @return $this
     */
    public function groupBy($columns)
    {
        if (is_array($columns)) {
            $this->groupByClause = implode(', ', $columns);
        } else {
            $this->groupByClause = $columns;
        }
        return $this;
    }

    /**
     * Set HAVING.
     * @param string $condition
     * @param mixed  $value
     * @return $this
     */
    public function having($condition, $value = null)
    {
        $this->havingClause = $condition;
        if ($value !== null) {
            $this->bindings[] = $value;
        }
        return $this;
    }

    /**
     * Set LIMIT.
     * @param int $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(int $limit, ?int $offset = null)
    {
        $this->limitValue = $limit;
        if ($offset !== null) {
            $this->offsetValue = $offset;
        }
        return $this;
    }

    /**
     * Conditional clause – executes callback only if condition is true.
     * @param bool $condition
     * @param callable $callback
     * @return $this
     */
    public function when($condition, callable $callback)
    {
        if ($condition) {
            $callback($this);
        }
        return $this;
    }

    // -------------------------------------------------------------------------
    // Execution Methods
    // -------------------------------------------------------------------------

    /**
     * Execute SELECT query and return all rows as array of objects.
     * @return array
     */
    public function get()
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->executeQuery($sql, $this->bindings);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $this->resetQuery();
        return $result;
    }

    /**
     * Execute SELECT query and return single row as object.
     * @return object|null
     */
    public function row()
    {
        $this->limit(1);
        $sql = $this->buildSelectQuery();
        $stmt = $this->executeQuery($sql, $this->bindings);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $this->resetQuery();

        return $row ?: null;
    }

    /**
     * Execute SELECT query and return all rows as plain array (non‑object).
     * @return array
     */
    public function result()
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->executeQuery($sql, $this->bindings);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->resetQuery();
        return $result;
    }

    /**
     * Get the number of rows.
     * @param string $field (optional, default '*')
     * @return int
     */
    public function count($field = '*')
    {
        $originalSelect = $this->selectFields;
        $this->selectFields = ["COUNT($field) AS count"];
        $sql = $this->buildSelectQuery();
        $stmt = $this->executeQuery($sql, $this->bindings);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $this->resetQuery();
        $this->selectFields = $originalSelect;
        return (int) ($row->count ?? 0);
    }

    /**
     * Insert a new record.
     * @param array $data associative array column => value
     * @return bool|string last insert ID on success, false on failure
     */
    public function insert(array $data)
    {
        if (empty($data) || !$this->table) {
            return false;
        }
        $columns = array_keys($data);
        $placeholders = ':' . implode(', :', $columns);
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ($placeholders)";

        $stmt = $this->conn->prepare($sql);
        // Bind values
        foreach ($data as $col => $val) {
            $stmt->bindValue(':' . $col, $val);
        }
        $success = $stmt->execute();
        $this->resetQuery();

        if ($success) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Alias for insert() – CodeIgniter style "save".
     */
    public function save(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Update records.
     * @param array $data associative array column => value
     * @return int number of affected rows
     */
    public function update(array $data = [])
    {
        // If using fluent set(), merge data
        if (!empty($data)) {
            $this->updateData = array_merge($this->updateData, $data);
            $this->updateBindings = array_merge($this->updateBindings, array_values($data));
        }

        if (empty($this->updateData) || !$this->table) {
            return 0;
        }

        $setParts = [];
        $setBindings = [];
        foreach ($this->updateData as $col => $val) {
            if (isset($this->updateRaw[$col])) {
                $setParts[] = "$col = $val";       // embed raw expression
            } else {
                $setParts[] = "$col = ?";
                $setBindings[] = $val;
            }
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        $sql .= $this->buildWhereClause();
        $sql .= $this->buildLikeClause();
        $sql .= $this->buildLimit();

        // Bindings order: SET values first, then WHERE/LIKE bindings
        $allBindings = array_merge($setBindings, $this->bindings);

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($allBindings);
        $affected = $stmt->rowCount();

        $this->resetQuery();
        return $affected;
    }

    public function _update(array $data)
    {
        if (empty($data) || !$this->table) {
            return 0;
        }
        $setParts = [];
        foreach ($data as $col => $val) {
            $setParts[] = "$col = :$col";
            $this->bindings[] = $val;
        }
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$this->table} SET $setClause";
        $sql .= $this->buildWhereClause();
        $sql .= $this->buildLikeClause();
        $sql .= $this->buildLimit();

        $stmt = $this->conn->prepare($sql);
        // Bind the update values
        foreach ($data as $col => $val) {
            $stmt->bindValue(':' . $col, $val);
        }
        // Bind where/like values
        foreach ($this->bindings as $index => $bindVal) {
            // Use numeric placeholders or named? We used raw values in bindings; simpler to pass to execute array.
            // But we have mixed keys. Let's re-bind using positional parameters.
            // Actually easier: use $stmt->execute($allBindings) after merging.
        }
        // Merge all bindings: update data + where/like bindings
        $allBindings = array_merge(array_values($data), $this->bindings);
        $success = $stmt->execute($allBindings);
        $affected = $stmt->rowCount();
        $this->resetQuery();
        return $affected;
    }

    /**
     * Delete records.
     * @return int number of affected rows
     */
    public function delete()
    {
        if (!$this->table) {
            return 0;
        }
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        $sql .= $this->buildLikeClause();
        $sql .= $this->buildLimit();

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($this->bindings);
        $affected = $stmt->rowCount();
        $this->resetQuery();
        return $affected;
    }

    // -------------------------------------------------------------------------
    // RAW QUERY METHODS
    // -------------------------------------------------------------------------

    /**
     * Execute a raw SQL query with bound parameters.
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     * @throws PDOException
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Execute raw query and return all rows as objects.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function queryGet(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Execute raw query and return a single row as object.
     * @param string $sql
     * @param array $params
     * @return object|null
     */
    public function queryRow(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row ?: null;
    }

    /**
     * Execute raw query and return all rows as associative arrays.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function queryResult(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------------------
    // JOIN METHODS
    // -------------------------------------------------------------------------

    /**
     * Add a JOIN clause.
     * @param string $table Table name with optional alias (e.g. "users u")
     * @param string $condition ON condition (e.g. "u.id = p.user_id")
     * @param string $type JOIN type: 'inner', 'left', 'right' (default 'inner')
     * @return $this
     */
    public function join(string $table, string $condition, string $type = 'inner')
    {
        $this->joinClauses[] = [
            'table' => $table,
            'condition' => $condition,
            'type' => $type
        ];
        return $this;
    }

    // Convenience shortcuts
    public function leftJoin(string $table, string $condition)
    {
        return $this->join($table, $condition, 'left');
    }

    public function rightJoin(string $table, string $condition)
    {
        return $this->join($table, $condition, 'right');
    }

    // -------------------------------------------------------------------------
    // TRANSACTION METHODS
    // -------------------------------------------------------------------------

    /**
     * Begin a transaction.
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit the current transaction.
     * @return bool
     */
    public function commit(): bool
    {
        return $this->conn->commit();
    }

    /**
     * Rollback the current transaction.
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->conn->rollBack();
    }

    /**
     * Execute a callback within a transaction.
     * Automatically rolls back if an exception is thrown, otherwise commits.
     *
     * @param callable $callback Function to execute. Receives $this as argument.
     * @return mixed The callback's return value.
     * @throws \Throwable
     */
    public function transaction(callable $callback)
    {
        try {
            $this->beginTransaction();
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function set($column, $value)
    {
        $this->updateData[$column] = $value;
        $this->updateBindings[] = $value;
        return $this;
    }

    /**
     * Set a column value for UPDATE, but do NOT bind the value.
     * The value will be embedded directly into the SQL (use with caution, avoid user input).
     *
     * @param string $column
     * @param string $expression  e.g. "TO_DATE('2026-05-02', 'YYYY-MM-DD')"
     * @return $this
     */
    public function setRaw(string $column, string $expression)
    {
        $this->updateData[$column] = $expression;
        $this->updateRaw[$column] = true;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Internal Helpers
    // -------------------------------------------------------------------------

    private function buildSelectQuery()
    {
        $fields = implode(', ', $this->selectFields);

        $sql = "SELECT {$fields} FROM {$this->table}";

        // ADD THIS BLOCK
        if (!empty($this->joinClauses)) {
            foreach ($this->joinClauses as $join) {
                $type = strtoupper($join['type']);
                $sql .= " {$type} JOIN {$join['table']} ON {$join['condition']}";
            }
        }

        $sql .= $this->buildWhereClause();
        $sql .= $this->buildLikeClause();

        if ($this->groupByClause) {
            $sql .= " GROUP BY {$this->groupByClause}";
        }

        if ($this->havingClause) {
            $sql .= " HAVING {$this->havingClause}";
        }

        if ($this->orderByClause) {
            $sql .= " ORDER BY {$this->orderByClause}";
        }

        $sql .= $this->buildLimit();

        return $sql;
    }

    private function buildWhereClause()
    {
        if (empty($this->whereClauses)) {
            return '';
        }
        $conditions = [];
        foreach ($this->whereClauses as $wc) {
            // Check if it's a raw NULL condition
            if (isset($wc[3]) && $wc[3] === true) {
                [$col, $op, $val] = $wc;
                $conditions[] = "$col $op NULL";   // no placeholder
            } else {
                [$col, $op, $val] = $wc;
                $conditions[] = "$col $op ?";
            }
        }
        return ' WHERE ' . implode(' AND ', $conditions);
    }

    private function _buildWhereClause()
    {
        if (empty($this->whereClauses)) {
            return '';
        }
        $conditions = [];
        foreach ($this->whereClauses as $wc) {
            [$col, $op, $val] = $wc;
            $conditions[] = "$col $op ?";
        }
        return ' WHERE ' . implode(' AND ', $conditions);
    }

    private function buildLikeClause()
    {
        if (empty($this->likeClauses)) {
            return '';
        }
        $likes = [];
        foreach ($this->likeClauses as $lc) {
            [$col, $val] = $lc;
            $likes[] = "$col LIKE ?";
        }
        return (empty($this->whereClauses) ? ' WHERE ' : ' AND ') . implode(' AND ', $likes);
    }

    private function buildLimit()
    {
        $limit = '';

        if ($this->limitValue !== null) {

            if ($this->offsetValue !== null) {

                $offset = (int)$this->offsetValue;
                $limitVal = (int)$this->limitValue;

                $limit = " OFFSET {$offset} ROWS FETCH NEXT {$limitVal} ROWS ONLY";

            } else {

                $limitVal = (int)$this->limitValue;

                $limit = " FETCH FIRST {$limitVal} ROWS ONLY";
            }
        }

        return $limit;
    }

    private function executeQuery($sql, $bindings)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    private function resetQuery()
    {
        $this->table         = null;
        $this->selectFields  = ['*'];
        $this->whereClauses  = [];
        $this->likeClauses   = [];
        $this->bindings      = [];
        $this->limitValue    = null;
        $this->offsetValue   = null;
        $this->orderByClause = null;
        $this->groupByClause = null;
        $this->havingClause  = null;
        $this->isUpdate      = false;
        $this->updateData    = [];
        $this->joinClauses   = [];
        $this->updateRaw     = [];
    }
}