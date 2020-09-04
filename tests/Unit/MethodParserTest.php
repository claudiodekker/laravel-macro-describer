<?php

namespace ClaudioDekker\MacroDescriber\Tests\Unit;

use ClaudioDekker\MacroDescriber\MethodParser;
use ClaudioDekker\MacroDescriber\Tests\TestCase;
use Illuminate\Support\Collection;

class MethodParserTest extends TestCase
{
    public function providesParameters(): array
    {
        return [
            [
                function (array $foo, ?array $bar, ?array ...$baz) {
                    //
                },
                [
                    'array $foo',
                    '?array $bar',
                    '?array ...$baz',
                ],
            ],
            [
                function (Collection $foo, ?Collection $bar, Collection $baz = null) {
                    //
                },
                [
                    'Illuminate\Support\Collection $foo',
                    '?Illuminate\Support\Collection $bar',
                    '?Illuminate\Support\Collection $baz = null',
                ],
            ],
            [
                function ($foo, $bar = -1, ...$baz) {
                    //
                },
                [
                    '$foo',
                    '$bar = -1',
                    '...$baz',
                ],
            ],
            [
                function ($foo = 'bar', array $bar = [], $baz = ['foo', 'bar']) {
                    //
                },
                [
                    '$foo = \'bar\'',
                    'array $bar = array()',
                    '$baz = array(\'foo\', \'bar\')',
                ],
            ],
            [
                function (string $foo = "That's crazy!", $bar = 'Another "string" example', $baz = "It's got \"both\"") {
                    //
                },
                [
                    'string $foo = "That\'s crazy!"',
                    '$bar = \'Another "string" example\'',
                    "\$baz = 'It\'s got \\\"both\\\"'",
                ],
            ],
            [
                function (bool $foo, bool $bar = true, bool $baz = false) {
                    //
                },
                [
                    'bool $foo',
                    'bool $bar = true',
                    'bool $baz = false',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providesParameters
     */
    public function it_correctly_resolves_the_parameters($signature, $expected): void
    {
        $test = MethodParser::getParameters($signature);
        $this->assertSame($expected, $test);
    }

    public function providesReturnType(): array
    {
        return [
            [
                function () {
                    //
                },
                'void',
            ],
            [
                function (): Collection {
                    //
                },
                'Illuminate\Support\Collection',
            ],
            [
                function (): ?Collection {
                    //
                },
                '?Illuminate\Support\Collection',
            ],
            [
                function (): self {
                    //
                },
                'self',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providesReturnType
     */
    public function it_correctly_determines_the_return_type($signature, $expected): void
    {
        $this->assertSame($expected, MethodParser::getReturnType($signature));
    }
}
