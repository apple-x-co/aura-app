<?php

declare(strict_types=1);

namespace AppCore\Application\Shared;

interface DbConnectionInterface
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
