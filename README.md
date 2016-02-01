# Mooti Testable

[![Build Status](https://travis-ci.org/mooti/xizlr-testable.svg?branch=master)](https://travis-ci.org/mooti/xizlr-testable)
[![Coverage Status](https://coveralls.io/repos/github/mooti/xizlr-testable/badge.svg?branch=master)](https://coveralls.io/github/mooti/xizlr-testable?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mooti/xizlr-testable/v/stable)](https://packagist.org/packages/mooti/xizlr-testable)
[![Total Downloads](https://poser.pugx.org/mooti/xizlr-testable/downloads)](https://packagist.org/packages/mooti/xizlr-testable)
[![Latest Unstable Version](https://poser.pugx.org/mooti/xizlr-testable/v/unstable)](https://packagist.org/packages/mooti/xizlr-testable)
[![License](https://poser.pugx.org/mooti/xizlr-testable/license?1)](https://packagist.org/packages/mooti/xizlr-testable)

A small repo to aid in creating simple clean testable code without the need to use dependancy injection container. It replaces the ```new``` keyword with a method call enabling you to easily mock objects.

### Installation

You can install this through packagist

```
$ composer require mooti/testable
```

### Usage

You have a class Foo that will be used within another class bar. Given you hav the following Foo.php

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

you will create the following Bar.php. You add the Testable trait and use the ```createNew``` method to instantiate a new object. The first argument is the name of the class and subsequent arguments are the constructor arguments. 

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
$bar->speak('Ken', 'Lalobo');

```

and we run it, we should see:

```
$ php bin/run.php
$ Hello Ken Lalobo
```

Now for tests. We can now create a partial mock of Bar and override the ```createNew``` method to return a mocked version of the Foo class. We can then set our expectations as normal.

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
