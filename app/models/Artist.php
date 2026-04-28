<?php

declare(strict_types=1);

namespace App\Models;

final class Artist extends BaseModel
{
    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM artists ORDER BY created_at DESC');
    }
}
