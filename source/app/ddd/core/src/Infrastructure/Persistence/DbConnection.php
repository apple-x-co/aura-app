<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Persistence;

use AppCore\Application\Shared\DbConnectionInterface;
use Aura\Sql\ExtendedPdoInterface;

final class DbConnection implements DbConnectionInterface
{
    public function __construct(
        private ExtendedPdoInterface $pdo,
    ) {
    }

    public function begin(): void
    {
        if ($this->pdo->inTransaction()) {
            return;
        }

        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        if (! $this->pdo->inTransaction()) {
            return;
        }

        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if (! $this->pdo->inTransaction()) {
            return;
        }

        $this->pdo->rollBack();
    }
}
