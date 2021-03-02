<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src'])
    ->name('*.php');

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        'combine_nested_dirname' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'dir_constant' => true,
        'ereg_to_preg' => true,
        'escape_implicit_backslashes' => false,
        'heredoc_indentation' => true,
        'function_to_constant' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'implode_call' => true,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'logical_operators' => true,
        'mb_str_functions' => true,
        'modernize_types_casting' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'native_constant_invocation' => true,
        'native_function_invocation' => true,
        'no_alias_functions' => true,
        'no_superfluous_elseif' => false,
        'no_superfluous_phpdoc_tags' => false,
        'no_unneeded_final_method' => true,
        'ordered_class_elements' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
        ],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_line_span' => [
            'const' => 'multi',
            'method' => 'multi',
            'property' => 'multi',
        ],
        'pow_to_exponentiation' => true,
        'random_api_migration' => true,
        'self_accessor' => true,
        'self_static_accessor' => true,
        'set_type_to_cast' => true,
        'single_line_throw' => false,
        'ternary_to_null_coalescing' => true,
    ]);
