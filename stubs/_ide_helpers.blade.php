<{{ "?php" }}

/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpUnusedAliasInspection */

@foreach($macros as $namespace => $resources)
namespace {!! $namespace !!} {

@foreach ($resources as $resource)
    /**
@foreach($resource['macros'] as $signature)
     * {{'@'}}method {!! $signature !!}
@endforeach
     */
    class {!! $resource['class'] !!}
    {
        //
    }
@endforeach

}
@endforeach
