<?php

$config = new Symfony\CS\Config\Config();

$config->getFinder()
    ->exclude('data')
    ->exclude('puphpet')
    ->exclude('public/css')
    ->exclude('public/img')
    ->exclude('public/js')
    ->exclude('vendor')
    ->in(__DIR__)
;

return $config
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->setUsingCache(true)
    ->fixers([
        'braces',
        'concat_with_spaces',
        'controls_spaces',
        // 'double_arrow_multiline_whitespaces',
        'duplicate_semicolon',
        'elseif',
        // 'empty_return',
        'encoding',
        'eof_ending',
        'extra_empty_lines',
        'function_call_space',
        'function_declaration',
        'include',
        'indentation',
        'join_function',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiline_array_trailing_comma',
        'multiline_spaces_before_semicolon',
        'multiple_use',
        'namespace_no_leading_whitespace',
        'new_with_braces',
        'object_operator',
        'operators_spaces',
        'ordered_use',
        'parenthesis',
        // 'phpdoc_indent',
        // 'phpdoc_params',
        'php_closing_tag',
        'psr0',
        'return',
        'remove_leading_slash_use',
        'remove_lines_between_uses',
        'single_array_no_trailing_comma',
        'short_array_syntax',
        'short_tag',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'trailing_spaces',
        'unused_use',
        'visibility',
        'whitespacy_lines',

    ])
;
