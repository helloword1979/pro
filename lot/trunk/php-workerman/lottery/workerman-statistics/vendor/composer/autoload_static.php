<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit20b9075184287ee0bb70701c5e18da76
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit20b9075184287ee0bb70701c5e18da76::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit20b9075184287ee0bb70701c5e18da76::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
