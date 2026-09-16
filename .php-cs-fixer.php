<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath('config/reference.php')
    ->notPath('castor.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP85Migration' => true,
        '@PHP8x5Migration:risky' => true,
        'global_namespace_import' => [
            'import_classes' => false,
            'import_functions' => false,
            'import_constants' => false,
        ],
        'native_function_invocation' => [
            'include' => ['@all'],
        ],
        'ordered_class_elements' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'trailing_comma_in_multiline' => [
            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ignore',
            'attribute_placement' => 'standalone',
        ],
        'no_extra_blank_lines' => [
            'tokens' => ['case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use'],
        ],
        'phpdoc_to_comment' => [
            'ignored_tags' => ['var', 'phpstan-return', 'phpstan-param'],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
