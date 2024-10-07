<?php

namespace Phproberto\Vite\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function getFixturePath($fixture)
    {
        return __DIR__ . '/fixtures/' . $fixture;
    }

    protected function setPropertyValue($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    protected function setStaticPropertyValue($class, $property, $value)
    {
        $reflection = new \ReflectionClass($class);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue(null, $value);
    }
}