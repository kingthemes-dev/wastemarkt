<?php

namespace YahnisElsts\PluginUpdateChecker\v5p4;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory as MajorFactory;
use YahnisElsts\PluginUpdateChecker\v5p4\PucFactory as MinorFactory;

require __DIR__ . '/update/v5p4/Autoloader.php';
new Autoloader();

require __DIR__ . '/update/v5p4/PucFactory.php';
require __DIR__ . '/update/v5/PucFactory.php';

//Register classes defined in this version with the factory.
foreach (
    array(
        'Plugin\\UpdateChecker' => Plugin\UpdateChecker::class,
        'Theme\\UpdateChecker'  => Theme\UpdateChecker::class,

        'Vcs\\PluginUpdateChecker' => Vcs\PluginUpdateChecker::class,
        'Vcs\\ThemeUpdateChecker'  => Vcs\ThemeUpdateChecker::class,

        'GitHubApi'    => Vcs\GitHubApi::class,
        'BitBucketApi' => Vcs\BitBucketApi::class,
        'GitLabApi'    => Vcs\GitLabApi::class,
    )
    as $pucGeneralClass => $pucVersionedClass
) {
    MajorFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.4');
    MinorFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.4');
}

MajorFactory::buildUpdateChecker(
    _HMWP_ACCOUNT_SITE_ . '/api/wp/update/',
    _HMWP_ROOT_DIR_ . '/index.php',
    'hide-my-wp' );