<?php

namespace ApplicationTest\Entity;

use Application\Entity;
use ApplicationTest\Mock\StringCastable;
use PHPUnit_Framework_TestCase;

class RepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorSetsValues()
    {
        $owner = 'foo';
        $name = 'bar';

        $entity = new Entity\Repository(
            $owner,
            $name
        );

        $this->assertSame($owner, $entity->owner());
        $this->assertSame($name, $entity->name());
    }

    public function testConstructorCastsToString()
    {
        $owner = new StringCastable('foo');
        $name = new StringCastable('bar');

        $entity = new Entity\Repository(
            $owner,
            $name
        );

        $this->assertSame((string) $owner, $entity->owner());
        $this->assertSame((string) $name, $entity->name());
    }
}
