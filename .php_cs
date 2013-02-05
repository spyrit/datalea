<?php 

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(array(
      'model/om',
      'model/map',
      'vendor',
      'web/images',
      'Symfony/Tests/Component/ClassLoader/ClassCollectionLoaderTest.php',
      'Symfony/Tests/Component/DependencyInjection/Fixtures/containers/container9.php',
      'Symfony/Tests/Component/DependencyInjection/Fixtures/includes/foo.php',
      'Symfony/Tests/Component/DependencyInjection/Fixtures/php/services9.php',
      'Symfony/Tests/Component/DependencyInjection/Fixtures/yaml/services9.yml',
      'Symfony/Tests/Component/Routing/Fixtures/dumper/url_matcher1.php',
      'Symfony/Tests/Component/Routing/Fixtures/dumper/url_matcher2.php',
      'Symfony/Tests/Component/Yaml/Fixtures/sfTests.yml',
    ))
    ->notName('/.*\.(ico|gif|png|jpeg|jpg|bmp|zip|gz|tar|7z|tiff|log|phar|jar)/')
    ->in(array(
        __DIR__.'/src',
    ))
;

return Symfony\CS\Config\Config::create()
    ->fixers(array(
        'indentation',
        'linefeed',
        'unused_use',
        'trailing_spaces',
        'php_closing_tag',
        'short_tag',
        'return',
        'visibility',
        'braces',
        'phpdoc_params',
        'eof_ending',
        'extra_empty_lines',
        'include',
        'psr0',
        'controls_spaces',
        'elseif',
    ))
    ->finder($finder)
;
