<?php

namespace App\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckLinksCommandTest extends KernelTestCase
{
    public function testSomething(): void
    {
        //TODO
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
    }
}
