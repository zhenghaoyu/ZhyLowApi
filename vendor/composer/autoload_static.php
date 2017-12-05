<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd8bc9227aa9c84f2681d800e8eb37431
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Finder\\' => 25,
        ),
        'R' => 
        array (
            'Remind\\Api\\' => 11,
        ),
        'G' => 
        array (
            'Gregwar\\' => 8,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
        'Remind\\Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Gregwar\\' => 
        array (
            0 => __DIR__ . '/..' . '/gregwar/captcha/src/Gregwar',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd8bc9227aa9c84f2681d800e8eb37431::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd8bc9227aa9c84f2681d800e8eb37431::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}