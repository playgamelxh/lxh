<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit97c555c6e6d747a403a0417e06adaa44
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Test\\' => 5,
        ),
        'S' => 
        array (
            'Src\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/test/test',
        ),
        'Src\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/index',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit97c555c6e6d747a403a0417e06adaa44::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit97c555c6e6d747a403a0417e06adaa44::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}