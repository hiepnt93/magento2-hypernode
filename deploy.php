<?php

namespace HipexDeployConfiguration;

$configuration = new ApplicationTemplate\Magento2(
    'git@github.com:ByteInternet/magento2.komkommer.store.git',
    ['en_US'],
    ['en_US']
);

$configuration->setPhpVersion('php'); // @NOTE(timon): is this ok? Normally this would be php74 or something but that doesn't apply for Hypernode.
$productionStage = $configuration->addStage(
    'production',
    'magento2.komkommer.store',
    'app'
);
$productionStage->addServer('hntestgroot.hypernode.io');

return $configuration;

