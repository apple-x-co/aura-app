<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Handler;

use AppCore\Exception\RuntimeException;
use Koriym\HttpConstants\CacheControl;
use Koriym\HttpConstants\ResponseHeader;
use MyVendor\MyPackage\RequestHandler;

use function array_merge;
use function fopen;
use function fputcsv;

final class Download extends RequestHandler
{
    /** @var array<string, string> */
    public array $headers = [ResponseHeader::CACHE_CONTROL => CacheControl::NO_STORE];

    public function onGet(): self
    {
        $stream = fopen('php://temp', 'wb');
        if ($stream === false) {
            throw new RuntimeException();
        }

        $this->headers = array_merge($this->headers, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=dummy.csv',
            'Content-Transfer-Encoding' => 'binary',
        ]);
        $this->stream = $stream;

        fputcsv($stream, [
            'ID',
            'NAME',
        ]);

        return $this;
    }
}
