<?php

if (!file_exists(__DIR__.'/src') || !file_exists(__DIR__.'/tests')) {
    exit(0);
}

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests'])
    ->exclude([
        'tmp'
    ])
;

return (new PhpCsFixer\Config())
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => [
            'header' => <<<EOF
This file is part of the SymfonyCasts ResetPasswordBundle package.
Copyright (c) SymfonyCasts <https://symfonycasts.com/>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF
        ],
        // Because of the commented out argument in ResetPasswordHelperInterface
        'no_superfluous_phpdoc_tags' => false,
    ))
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
