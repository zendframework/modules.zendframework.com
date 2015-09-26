<?php

$config = new Symfony\CS\Config\Config();

$config->getFinder()
    ->exclude('data')
    ->exclude('puphpet')
    ->name('*.php.dist')
    ->in(__DIR__)
;

return $config
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->setUsingCache(true)
    ->fixers([
        'concat_with_spaces',
        // 'double_arrow_multiline_whitespaces',
        'duplicate_semicolon',
        // 'empty_return',
        'extra_empty_lines',
        'include',
        'join_function',
        'multiline_array_trailing_comma',
        // 'multiline_spaces_before_semicolon',
        'namespace_no_leading_whitespace',
        'new_with_braces',
        'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'object_operator',
        'operators_spaces',
        'ordered_use',
        // 'phpdoc_indent',
        // 'phpdoc_params',
        'remove_leading_slash_use',
        'remove_lines_between_uses',
        'return',
        'single_array_no_trailing_comma',
        'short_array_syntax',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'unused_use',
        'whitespacy_lines',
    ])
;
