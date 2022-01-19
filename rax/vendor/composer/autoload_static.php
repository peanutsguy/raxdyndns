<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit01bf5111f06aa032412eb3c93e09bb5e
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'mikemccabe\\JsonPatch\\' => 21,
        ),
        'S' => 
        array (
            'Symfony\\Component\\EventDispatcher\\' => 34,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'mikemccabe\\JsonPatch\\' => 
        array (
            0 => __DIR__ . '/..' . '/mikemccabe/json-patch-php/src',
        ),
        'Symfony\\Component\\EventDispatcher\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/event-dispatcher',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'O' => 
        array (
            'OpenCloud' => 
            array (
                0 => __DIR__ . '/..' . '/rackspace/php-opencloud/lib',
            ),
        ),
        'G' => 
        array (
            'Guzzle\\Tests' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/tests',
            ),
            'Guzzle' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit01bf5111f06aa032412eb3c93e09bb5e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit01bf5111f06aa032412eb3c93e09bb5e::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit01bf5111f06aa032412eb3c93e09bb5e::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
