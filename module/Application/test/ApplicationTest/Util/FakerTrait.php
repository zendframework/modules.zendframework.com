<?php

namespace ApplicationTest\Util;

use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @return Generator
     */
    private function faker()
    {
        if (null === $this->faker) {
            $this->faker = Factory::create('en_US');
            $this->faker->seed(9000);
        }

        return $this->faker;
    }
}
