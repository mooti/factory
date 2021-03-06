<?php

namespace Mooti\Factory;

use Mooti\Factory\Exception\ClassNotFoundException;

trait Factory
{
    private $objects = [];

    /**
     * Add an instance of a class. This will be returned when the user tries to create a the object
     *
     * @param string $className  The class to create
     * @param string $mockObject The class to create
     *
     */
    public function addInstance($className, $mockObject)
    {
        if(!isset($this->objects[$className])) {
            $this->objects[$className] = [];
        }
        $this->objects[$className][] = $mockObject;
    }

    /**
     * Create a new instance of a given class
     *
     * @param string $className The class to create
     *
     * @return object The new class
     */
    public function createNew($className)
    {
        $constructArguments = func_get_args();
        return $this->_createNew( ...$constructArguments);
    }

    /**
     * Create a new instance of a given class
     *
     * @param string $className The class to create
     *
     * @return object The new class
     */
    protected function _createNew($className)
    {
        if (!empty($this->objects[$className])) {
            return array_shift($this->objects[$className]);
        }

        if (class_exists($className) == false) {
            throw new ClassNotFoundException('The class '.$className.' cannot be found');
        }

        $constructArguments = func_get_args();
        array_shift($constructArguments);
        return new $className( ...$constructArguments);
    }
}