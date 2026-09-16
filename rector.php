<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Attribute\SortAttributeNamedArgsRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\FunctionLike\NarrowWideUnionReturnTypeRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Transform\Rector\Attribute\AttributeKeyToClassConstFetchRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
    ])
    ->withSkip([
        // both re-print the whole attribute/call and flatten hand-tuned formatting; this repo
        // is meant to be read on a slide, so layout wins over argument ordering
        SortAttributeNamedArgsRector::class,
        SortCallLikeNamedArgsRector::class,
        ExplicitNullableParamTypeRector::class,
        PreferPHPUnitThisCallRector::class,
        AttributeKeyToClassConstFetchRector::class => [
            __DIR__.'/src/*/Domain/*',
        ],
        // a `readonly class` cannot be a PHP lazy object, so an entity that becomes one can no
        // longer be proxied by Doctrine. Readonly *properties* are fine; readonly entities are a
        // trap waiting for the first ManyToOne that points at them.
        ReadOnlyClassRector::class => [
            __DIR__.'/src/*/Domain/Model/*',
        ],
        // narrows `self|array` to `array` even though the method returns `new self(...)` on one
        // branch — a miscompilation, not a narrowing
        NarrowWideUnionReturnTypeRector::class => [
            __DIR__.'/src/Shared/Infrastructure/ApiPlatform/State/Paginator.php',
        ],
    ])
;
