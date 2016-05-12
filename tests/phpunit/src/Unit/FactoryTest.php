<?php
namespace Mooti\Test\PHPUnit\Factory\Unit;

use Mooti\Test\PHPUnit\Factory\Unit\Fixture\TestClass;
use Mooti\Factory\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createNewSucceeds()
    {
        $testableObject = $this->getMockForTrait(Factory::class);

        $testObject = $testableObject->createNew(TestClass::class);

        self::assertInstanceOf(TestClass::class, $testObject);
        self::assertEquals('foo', $testObject->sayFoo());
    }

    /**
     * @test
     * @expectedException Mooti\Factory\Exception\ClassNotFoundException
     * @expectedExceptionMessage The class \NoClassHere cannot be found
     */
    public function createNewWithNonExistingClassThrowsClassNotFoundException()
    {
        $testableObject = $this->getMockForTrait(Factory::class);

        $testObject = $testableObject->createNew('\\NoClassHere');
    }

    /**
     * @test
     */
    public function addInstanceSucceeds()
    {
        $testableObject = $this->getMockForTrait(Factory::class);

        $mockObject1 = $this->getMockBuilder(TestClass::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockObject1->expects(self::once())
            ->method('sayFoo')
            ->will(self::returnValue('foo1'));

        $mockObject2 = $this->getMockBuilder(TestClass::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockObject2->expects(self::once())
            ->method('sayFoo')
            ->will(self::returnValue('foo2'));

        $testableObject->addInstance(TestClass::class, $mockObject1);
        $testableObject->addInstance(TestClass::class, $mockObject2);

        $returnedMockObject1 = $testableObject->createNew(TestClass::class);
        $returnedMockObject2 = $testableObject->createNew(TestClass::class);

        self::assertSame($mockObject1, $returnedMockObject1);
        self::assertSame($mockObject2, $returnedMockObject2);
        self::assertNotSame($mockObject1, $mockObject2);
        self::assertEquals('foo1', $mockObject1->sayFoo());
        self::assertEquals('foo2', $mockObject2->sayFoo());
    }

}
