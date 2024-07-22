<?php

declare(strict_types=1);

namespace AppCore\Domain\FlashMessenger;

interface FlashMessengerInterface
{
    public function set(FlashMessageType $type, string $text): void;

    public function get(FlashMessageType $type, string|null $alternative = null): string|null;
}
