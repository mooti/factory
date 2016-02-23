<?php

namespace Mooti\Xizlr\Testable;

trait Testable
{
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
        $constructArguments = func_get_args();
        array_shift($constructArguments);
        return new $className( ...$constructArguments);
    }
}
