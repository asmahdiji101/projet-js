<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $result = $statement->fetch();

        return $result === false ? null : $result;
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $statement = $this->db->prepare($sql);

        return $statement->execute($params);
    }
}
