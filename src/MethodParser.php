<?php

namespace ClaudioDekker\MacroDescriber;

use Illuminate\Support\Str;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use UnexpectedValueException;

class MethodParser
{
    /**
     * Generates all PHPDoc-style parameters.
     *
     * @param  callable  $method
     * @return array
     * @throws ReflectionException
     */
    public static function getParameters(callable $method): array
    {
        $reflector = new ReflectionFunction($method);

        $parameters = [];

        foreach ($reflector->getParameters() as $parameter) {
            $parameters[] = self::getParameter($parameter);
        }

        return $parameters;
    }

    /**
     * Generate a single PHPDoc-style parameter.
     *
     * @param  ReflectionParameter  $parameter
     * @return string
     * @throws ReflectionException
     */
    protected static function getParameter(ReflectionParameter $parameter): string
    {
        $variadic = $parameter->isVariadic() ? '...' : '';
        $name = $parameter->getName();

        $value = '';
        if ($parameter->isDefaultValueAvailable()) {
            $value = ' = '.self::parseValue($parameter->getDefaultValue());
        }

        if (! $type = $parameter->getType()) {
            return $variadic.'$'.$name.$value;
        }

        // https://www.php.net/manual/en/reflectiontype.tostring.php#125211
        if ($type instanceof ReflectionNamedType) {
            $type = $type->getName();
        }

        $optional = $parameter->allowsNull() ? '?' : '';

        return $optional.$type.' '.$variadic.'$'.$name.$value;
    }

    /**
     * Generate the PHPDoc-style return type.
     *
     * @param  callable  $method
     * @return string
     * @throws ReflectionException
     */
    public static function getReturnType(callable $method): string
    {
        $reflector = new ReflectionFunction($method);

        if (! $type = $reflector->getReturnType()) {
            return 'void';
        }

        $optional = $type->allowsNull() ? '?' : '';

        if ($type instanceof ReflectionNamedType) {
            return $optional.$type->getName();
        }

        return $optional.$type;
    }

    /**
     * Parse a single value type.
     *
     * @param $value
     * @return string
     * @throws UnexpectedValueException
     */
    private static function parseValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            $singleQuote = Str::contains($value, "'");
            $doubleQuote = Str::contains($value, '"');
            $escapedSingleQuote = Str::contains($value, "\'");
            $escapedDoubleQuote = Str::contains($value, '\"');

            if ($singleQuote && (! $doubleQuote || $escapedDoubleQuote)) {
                return '"'.$value.'"';
            } elseif ($doubleQuote && (! $singleQuote || $escapedSingleQuote)) {
                return "'$value'";
            }

            return  "'".str_replace(["'", '"'], ["\'", '\"'], $value)."'";
        }

        if (is_array($value)) {
            $values = [];
            foreach ($value as $entry) {
                $values[] = self::parseValue($entry);
            }

            return 'array('.implode(', ', $values).')';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        throw new UnexpectedValueException($value);
    }
}
