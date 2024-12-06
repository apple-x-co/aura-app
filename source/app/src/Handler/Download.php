<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use AppCore\Exception\RuntimeException;
use MyVendor\MyPackage\RequestHandler;

use function fopen;
use function fputcsv;

final class Download extends RequestHandler
{
    public function onGet(): self
    {
        $stream = fopen('php://temp', 'wb');
        if ($stream === false) {
            throw new RuntimeException();
        }

        $this->headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=dummy.csv',
            'Content-Transfer-Encoding' => 'binary',
        ];
        $this->stream = $stream;

        fputcsv($stream, [
            'ID',
            'NAME',
        ]);

        return $this;
    }
}
