<?php

declare(strict_types=1);

namespace Hypernode\DeployConfiguration;

use Hypernode\DeployConfiguration\PlatformConfiguration\HypernodeSettingConfiguration;

use function Deployer\{after, before, invoke, run, task};

$configuration = new ApplicationTemplate\Magento2(['en_US']);
$configuration->addPlatformConfiguration(
    new HypernodeSettingConfiguration('php_version', '8.1')
);

task('magento:prepare_env:acceptance', static function () {
    run('cp ~/apps/magento2.komkommer.store/shared/app/etc/env.php {{release_path}}/app/etc/env.php');
    run('cd {{release_path}}; n98-magerun2 config:env:set db.connection.default.host mysqlmaster');
    invoke('magento:cache:flush');
})->select('stage=acceptance');

task('magento:configure_env:acceptance', static function () {
    run('{{bin/php}} {{release_path}}/bin/magento config:set web/unsecure/base_url https://{{hostname}}/');
    run('{{bin/php}} {{release_path}}/bin/magento config:set web/secure/base_url https://{{hostname}}/');
    invoke('magento:cache:flush');
})->select('stage=acceptance');

before('magento:config:import', 'magento:prepare_env:acceptance');
after('magento:config:import', 'magento:configure_env:acceptance');

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
