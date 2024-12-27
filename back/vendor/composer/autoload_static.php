<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit64fd9d25983b7f36df18b18e23f3931d
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit64fd9d25983b7f36df18b18e23f3931d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit64fd9d25983b7f36df18b18e23f3931d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit64fd9d25983b7f36df18b18e23f3931d::$classMap;

        }, null, ClassLoader::class);
    }
}
