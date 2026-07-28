<?php

namespace Tests\Unit;

use Tests\TestCase;

class ApplicationSmokeTest extends TestCase
{
    public function test_application_boots(): void
    {
        $this->assertTrue(app()->bound('router'));
        $this->assertTrue(app()->bound('db'));
    }
}
