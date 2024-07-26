<?php

declare(strict_types=1);

namespace MyVendor\MyPackage;

use PHPUnit\Framework\TestCase;

class MyPackageTest extends TestCase
{
    protected MyPackage $myPackage;

    protected function setUp(): void
    {
        $this->myPackage = new MyPackage();
    }

    public function testIsInstanceOfMyPackage(): void
    {
        $actual = $this->myPackage;
        $this->assertInstanceOf(MyPackage::class, $actual);
    }
}
