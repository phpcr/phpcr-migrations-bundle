<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src/')
    ->in('tests/');
$config = new PhpCsFixer\Config();

return $config->setFinder($finder)
    ->setRules([
         '@Symfony' => true,
         'array_syntax' => ['syntax' => 'short'],
    ]);
