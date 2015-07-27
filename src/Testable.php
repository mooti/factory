<?php

namespace Mooti\Xizlr\Testable;

trait Testable
{

    /**
     * Create a new instance of a given class
     *
     * @param string $className          The class to create
     *
     * @return object The new class
     */
    public function instantiate($className)
    {
        $constructArguments = func_get_args();
        array_shift($constructArguments);
        return new $className( ...$constructArguments);
    }
}
