# Mooti Testable

[![Build Status](https://travis-ci.org/mooti/testable.svg?branch=master)](https://travis-ci.org/mooti/testable)
[![Coverage Status](https://coveralls.io/repos/github/mooti/testable/badge.svg?branch=master)](https://coveralls.io/github/mooti/testable?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mooti/testable/v/stable)](https://packagist.org/packages/mooti/testable)
[![Total Downloads](https://poser.pugx.org/mooti/testable/downloads)](https://packagist.org/packages/mooti/testable)
[![Latest Unstable Version](https://poser.pugx.org/mooti/testable/v/unstable)](https://packagist.org/packages/mooti/testable)
[![License](https://poser.pugx.org/mooti/testable/license)](https://packagist.org/packages/mooti/testable)

A small repo to aid in creating simple clean testable code without the need to use a dependancy injection container. It replaces the ```new``` keyword with a method call enabling you to easily mock objects.

### Installation

You can install this through packagist:

```
$ composer require mooti/testable
```

### Run the tests

If you would like to run the tests. Use the following:

```
$ ./bin/vendor/phpunit -c config/phpunit.xml
```

### Usage

Say you have a class Foo that will be used within another class Bar. Given you have the following Foo.php.

```php
<?php

namespace My;

class Foo
{
	private $firstName;
	private $lastName;

	public function __construct($firstName, $lastName) {
		$this->firstName = $firstName;
		$this->lastName  = $lastName;
	}

	public function hello()
	{
		return 'hello '.$this->firstName. ' ' . $this->lastName;
	}
}

```

You will create Bar.php. You can then add the Testable trait and use the ```createNew``` method to instantiate a new object. The first argument is the name of the class and subsequent arguments are the classes constructor arguments. 

```php
<?php

namespace Your;

use Mooti\Xizlr\Testable\Testable;
use My\Foo;

class Bar
{
	use Testable;

	public function speak($firstName, $lastName)
	{
		$foo = $this->createNew(Foo::class, $firstName, $lastName);
		return $foo->hello();
	}
}
```

So if you have the following script called run.php in you bin directory (assuming you are using composer's autoload)

```php
<?php
require_once('../vendor/autoload.php');

$bar = new \Your\Bar();
echo $bar->speak('Ken', 'Lalobo');

```

and we run it, we should see:

```
$ php bin/run.php
$ Hello Ken Lalobo
```

Now for tests. There are two ways in which we can write our tests:

#### Partial Mock

We can now create a partial mock of Bar and override the ```createNew``` method to return a mocked version of the Foo class. We can then set our expectations as normal.

```php
<?php
require_once('../vendor/autoload.php');

use My\Foo;
use Your\Bar;

class BarTest extends \PHPUnit_Framework_TestCase
{
	/**
     * @test
     */
    public function speakSucceeds()
    {
    	$firstName = 'Ken';
    	$lastName  = 'Lalobo';
    	$greeting  = 'Hello Ken Lalobo';

        $foo = $this->getMockBuilder(Foo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $foo->expects(self::once())
            ->method('hello')
            ->will(self::returnValue($greeting));

        $bar = $this->getMockBuilder(Bar::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $bar->expects(self::once())
            ->method('createNew')
            ->with(
                self::equalTo(Foo::class),
                self::equalTo($firstName),
                self::equalTo($lastName)
            )
            ->will(self::returnValue($foo));

        self::assertSame($greeting, $bar->speak($firstName, $lastName));
    }
}
```

#### Injection

Alternativley we can inject the mock of Foo into Bar using the ```addMock``` method. When you call ```createNew``` it will return the mocked version of the Foo class. We can then set our expectations as normal.

```php
<?php
require_once('../vendor/autoload.php');

use My\Foo;
use Your\Bar;

class BarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function speakSucceeds()
    {
        $firstName = 'Ken';
        $lastName  = 'Lalobo';
        $greeting  = 'Hello Ken Lalobo';

        $foo = $this->getMockBuilder(Foo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $foo->expects(self::once())
            ->method('hello')
            ->will(self::returnValue($greeting));

        $bar = new Bar;

        $bar->addMock(Foo::class, $foo);

        self::assertSame($greeting, $bar->speak($firstName, $lastName));
    }
}
```

If you need multiple objects of the same class you can still add them and set their individual expectations. They will be returned back in the order they weere added