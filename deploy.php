<?php

declare(strict_types=1);

namespace Hypernode\DeployConfiguration;

use Hypernode\DeployConfiguration\PlatformConfiguration\HypernodeSettingConfiguration;

use function Deployer\{after, before, invoke, run, task};

$configuration = new ApplicationTemplate\Magento2(['en_US']);

$configuration->addPlatformConfiguration(
    new HypernodeSettingConfiguration('php_version', '8.3')
);

$configuration->setPlatformConfigurations([
    new PlatformConfiguration\HypernodeSettingConfiguration('supervisor_enabled', 'True'),
    new PlatformConfiguration\HypernodeSettingConfiguration('rabbitmq_enabled', 'True'),
    new PlatformConfiguration\HypernodeSettingConfiguration('elasticsearch_enabled', 'False'),
    new PlatformConfiguration\HypernodeSettingConfiguration('opensearch_enabled', 'True'),
    new PlatformConfiguration\HypernodeSettingConfiguration('varnish_enabled', 'True'),
    new PlatformConfiguration\HypernodeSettingConfiguration('nodejs_version', '20'),
]);

//$configuration->setPlatformConfigurations([
//    new PlatformConfiguration\CronConfiguration('etc/cron')
//]);

//$configuration->setPlatformConfigurations([
//    new PlatformConfiguration\NginxConfiguration('etc/nginx')
//]);

//$configuration->setPlatformConfigurations([
//    new PlatformConfiguration\SupervisorConfiguration('etc/supervisor')
//]);


$productionStage = $configuration->addStage('production', 'whillstag.hypernode.io');
$productionStage->addServer('whillstag.hypernode.io');


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
