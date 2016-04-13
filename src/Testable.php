<?php

namespace Mooti\Testable;

trait Testable
{
    private $mocks = [];

    /**
     * Add a mock object. This will be returned when the user tries to create a new object
     *
     * @param string $className  The class to create
     * @param string $mockObject The class to create
     *
     */
    public function addMock($className, $mockObject)
    {
        if(!isset($this->mocks[$className])) {
            $this->mocks[$className] = [];
        }
        $this->mocks[$className][] = $mockObject;
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
        return $this->_createNew($className);
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
        if (!empty($this->mocks[$className])) {
            return array_shift($this->mocks[$className]);
        }

        $constructArguments = func_get_args();
        array_shift($constructArguments);
        return new $className( ...$constructArguments);
    }
}