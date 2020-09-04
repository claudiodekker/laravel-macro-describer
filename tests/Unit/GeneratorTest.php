<?php

namespace ClaudioDekker\MacroDescriber\Tests\Unit;

use ClaudioDekker\MacroDescriber\Tests\TestCase;

class GeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists($file = $this->package_path('_ide_helpers.php'))) {
            unlink($file);
        }
    }

    /** @test */
    public function it_generates_ide_helpers_for_all_registered_macros(): void
    {
        self::assertFalse(file_exists($this->package_path('_ide_helpers.php')));

        $this->artisan('macro:generate-helpers');

        self::assertTrue(file_exists($this->package_path('_ide_helpers.php')));
    }
}
