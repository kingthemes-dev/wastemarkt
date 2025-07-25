<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1f533d21220588991b50c63e1b5644ce
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RadisuTheme\\ClassifiedListingToolkits\\' => 38,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RadisuTheme\\ClassifiedListingToolkits\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1f533d21220588991b50c63e1b5644ce::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1f533d21220588991b50c63e1b5644ce::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1f533d21220588991b50c63e1b5644ce::$classMap;

        }, null, ClassLoader::class);
    }
}
