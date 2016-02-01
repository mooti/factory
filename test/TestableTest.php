<?php

namespace Mooti\Test\Xizlr\Testable;

use Mooti\Test\Xizlr\Testable\TestClass;

class BlockControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createNewSucceeds()
    {
        $testableObject = $this->getMockForTrait('\\Mooti\\Xizlr\\Testable\\Testable');

        self::assertInstanceOf(TestClass::class, $testableObject->createNew(TestClass::class));
    }

}
