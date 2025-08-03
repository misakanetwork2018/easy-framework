<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Helper\Arr;
use PDO;
use PDOStatement;

class DB
{
    public readonly PDO $pdo;

    public function __construct($configs = [])
    {
        ['host' => $host, 'port' => $port, 'user' => $user, 'pass' => $pass,
            'name' => $db, 'charset' => $charset] = $configs;

        $this->pdo = new PDO("mysql:dbname=$db;host=$host;port=$port;charset=$charset", $user, $pass);

        if (App::config('debug'))
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 获取最后插入的id
     *
     * @return string
     */
    public function lastInsertId(): string
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
    public function query($sql, array $binds = [], bool $selectOne = false, ?callable $each = null): mixed
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
    public function exec($sql, array $binds = [], bool $getEffectRowNum = false): bool|int
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
    private function do($sql, array $binds = []): array
    {
        $stmt = $this->pdo->prepare($sql);

        if (is_bool($stmt))
            return [null, $stmt];

        $bool = $stmt->execute(Arr::wrap($binds));

        return [$stmt, $bool];
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}