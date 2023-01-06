<?php

declare(strict_types=1);

namespace Hypernode\DeployConfiguration;

use Hypernode\DeployConfiguration\PlatformConfiguration\HypernodeSettingConfiguration;

use function Deployer\{before, run, task};

$configuration = new ApplicationTemplate\Magento2(['en_US']);
$configuration->addPlatformConfiguration(
    new HypernodeSettingConfiguration('php_version', '8.1')
);

task('copy:production_env', static function () {
    run('cp ~/apps/magento2.komkommer.store/shared/app/etc/env.php {{release_path}}/app/etc/env.php');
})->select('stage=acceptance');

before('magento:config:import', 'copy:production_env');

$stagingStage = $configuration->addStage('staging', 'staging.magento2.komkommer.store', 'hypernode');
$stagingStage->addServer('production1135-hypernode.hipex.io');

$productionStage = $configuration->addStage('production', 'magento2.komkommer.store');
$productionStage->addServer('hntestgroot.hypernode.io');

$acceptanceStage = $configuration->addStage('acceptance', 'acceptance.komkommer.store');
$acceptanceStage->addBrancherServer('hntestgroot')
    ->setLabels(['stage=acceptance', 'ci_ref=' . (\getenv('GITHUB_HEAD_REF') ?: 'none')]);

$configuration->setSharedFiles([
    'app/etc/env.php',
    'pub/errors/local.xml',
    '.user.ini',
    'pub/.user.ini'
]);

$configuration->setSharedFolders([
    'var/log',
    'var/session',
    'var/report',
    'var/export',
    'pub/media',
    'pub/sitemaps',
    'pub/static/_cache'
]);

return $configuration;
