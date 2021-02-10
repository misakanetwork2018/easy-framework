<?php

use Helper\Arr;

class DB
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct($configs = [])
    {
        ['host' => $host, 'port' => $port, 'user' => $user, 'pass' => $pass,
            'name' => $db, 'charset' => $charset] = $configs;

        $this->pdo = new PDO("mysql:dbname=$db;host=$host;port=$port;charset=$charset", $user, $pass);

        if (App::config('debug'))
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 获取PDO对象
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * 获取最后插入的id
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 查询
     *
     * @param $sql
     * @param array $binds
     * @param bool $selectOne
     * @param callable|null $each
     * @return array|mixed|null
     */
    public function query($sql, $binds = [], $selectOne = false, ?callable $each = null)
    {
        [$stmt, $bool] = $this->do($sql, $binds); /* @var PDOStatement $stmt */

        if (!$bool || !$stmt instanceof PDOStatement) return null;

        if ($selectOne) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } elseif (is_callable($each)) {
            $data = [];
            while ($single = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $each($single);
            }
            return $data;
        } else {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * 执行
     *
     * @param $sql
     * @param array $binds
     * @param bool $getEffectRowNum
     * @return int|bool
     */
    public function exec($sql, $binds = [], $getEffectRowNum = false)
    {
        [$stmt, $bool] = $this->do($sql, $binds); /* @var PDOStatement $stmt */

        if ($getEffectRowNum) {
            return $bool ? $stmt->rowCount() : 0;
        } else {
            return $bool;
        }
    }

    /**
     * 执行sql
     *
     * @param $sql
     * @param array $binds
     * @return array
     */
    private function do($sql, $binds = [])
    {
        $stmt = $this->pdo->prepare($sql);

        if (is_bool($stmt))
            return [null, $stmt];

        $bool = $stmt->execute(Arr::wrap($binds));

        return [$stmt, $bool];
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}