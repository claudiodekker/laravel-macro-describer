<?php

namespace ClaudioDekker\MacroDescriber\Commands;

use ClaudioDekker\MacroDescriber\MethodParser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class Generator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macro:generate-helpers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerates all Laravel::mixin() autocompletion helpers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $macros = $this->gatherNamespacedMacros();

        $contents = $this->generateIdeHelper($macros);

        file_put_contents(__DIR__.'/../../_ide_helpers.php', $contents);
    }

    /**
     * Gathers all macros defined in the application.
     *
     * @return Collection
     */
    protected function gatherNamespacedMacros(): Collection
    {
        return Collection::make(get_declared_classes())
            ->filter(function ($className) {
                return $this->usesMacroableTrait($className);
            })
            ->map(function ($className) {
                [$namespaceName, $shortName] = $this->splitClassNamespace($className);

                return [
                    'namespace' => $namespaceName,
                    'class' => $shortName,
                    'macros' => $this->getClassMacros($className),
                ];
            })->filter(static function ($item) {
                return count($item['macros']);
            })->groupBy('namespace');
    }

    /**
     * Determine whether the given class uses the Macroable trait.
     * @see Macroable
     *
     * @param $className
     * @return bool
     */
    protected function usesMacroableTrait($className): bool
    {
        return ! $this->isPhpInternal($className) && isset(class_uses($className)[Macroable::class]);
    }

    /**
     * Determine whether the class is a PHP internal or PHP extension defined class.
     *
     * @param $className
     * @return bool
     */
    protected function isPhpInternal($className): bool
    {
        try {
            return call_user_func([new ReflectionClass($className), 'isInternal']);
        } catch (ReflectionException $exception) {
            return true;
        }
    }

    /**
     * Retrieves all Macros defined on the given class.
     *
     * @param $className
     * @return array
     */
    protected function getClassMacros($className): array
    {
        try {
            $reflector = new ReflectionClass($className);
            $properties = $reflector->getStaticProperties();

            return Collection::make($properties['macros'] ?? [])
                ->map(function ($callable, $method) {
                    return $this->createSignature($method, $callable);
                })
                ->all();
        } catch (ReflectionException $exception) {
            return [];
        }
    }

    /**
     * Determines the Namespace that the Class is in.
     *
     * @param $className
     * @return array
     */
    protected function splitClassNamespace($className): array
    {
        $namespaceName = '';
        $shortName = '';

        try {
            $reflector = new ReflectionClass($className);

            $namespaceName = $reflector->getNamespaceName();
            $shortName = $reflector->getShortName();
        } catch (ReflectionException $exception) {
            //
        }

        return [$namespaceName, $shortName];
    }

    /**
     * Render our actual _ide_helper file.
     *
     * @param  Collection  $macros
     * @return string
     * @throws \Throwable
     */
    protected function generateIdeHelper(Collection $macros): string
    {
        $response = view('macro-describer::_ide_helpers', [
            'macros' => $macros,
        ])->render();

        if (is_array($response)) {
            return '';
        }

        return $response;
    }

    /**
     * Generate a method's full PHPDoc signature.
     *
     * @param  string  $method
     * @param  callable  $macro
     * @return string
     */
    protected function createSignature(string $method, callable $macro): string
    {
        $signature = $method.'(';

        try {
            $returnType = MethodParser::getReturnType($macro);
            $parameters = MethodParser::getParameters($macro);

            $signature = $returnType.' '.$signature.implode(', ', $parameters);
        } catch (ReflectionException | UnexpectedValueException $e) {
            //
        }

        return $signature.')';
    }
}
