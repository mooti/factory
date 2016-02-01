# Mooti Testable

[![Build Status](https://travis-ci.org/mooti/xizlr-testable.svg?branch=master)](https://travis-ci.org/mooti/xizlr-testable)

A small repo to aid in creating simple clean testable code without the need to use dependancy injection container. It replaces the ```new``` keyword with a method call enabling you to easily mock objects.

### Usage

Foo.php

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

Bar.php

```php
<?php

namespace Your;

use \Mooti\Xizlr\Testable\Testable;

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

Run.php

```php
<?php
require_once('Foo.php');
require_once('Bar.php');

$bar = new \Your\Bar();
$bar->speak('Ken', 'Lalobo');

```

If we run it then we should see:

```
> php Run.php
> Hello Ken Lalobo
```

Test.php

```php
<?php
require_once('Foo.php');
require_once('Bar.php');

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