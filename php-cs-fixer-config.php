<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src'])
    ->name('*.php');

$config = new PhpCsFixer\Config();

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'declare_strict_types' => true,
        'general_phpdoc_annotation_remove' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'increment_style' => ['style' => 'post'],
        'is_null' => true,
        'list_syntax' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'native_constant_invocation' => true,
        'native_function_invocation' => true,
        'no_unused_imports' => true,
        'no_superfluous_phpdoc_tags' => false,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_private',
                'constant_protected',
                'constant_public',
                'property_private',
                'property_protected',
                'property_public',
                'construct',
                'destruct',
                'magic',
            ],
            'sort_algorithm' => 'none',
        ],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_line_span' => [
            'property' => 'multi',
            'method' => 'multi',
        ],
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ]);
