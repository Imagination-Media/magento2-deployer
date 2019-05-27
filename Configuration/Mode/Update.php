<?php

/**
 * Magento 2 Deployer
 *
 * Deployment tool for Magento 2 by Imagination Media.
 *
 * @package ImaginationMedia\Deployer
 * @author Igor Ludgero Miura <igor@imaginationmedia.com>
 * @copyright Copyright (c) 2019 Imagination Media (https://www.imaginationmedia.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Deployer;

task('deploy:update:actions', function () {
    $configurationHelper = new \Deployer\Helper\Configuration();
    invoke_custom('deploy:prepare');
    invoke_custom('deploy:lock');
    invoke_custom('deploy:actions:before');
    invoke_custom('deploy:release');
    invoke_custom('create:release:from:last:release');
    invoke_custom('git:update:base:code');
    invoke_custom('composer:install');
    invoke_custom('generated:db:schema');

    if ($configurationHelper->isSetupUpgradeNecessary()) {
        invoke_custom('magento:upgrade:keep:generated');
    }

    if ((int)get("is_production") === 1) {
        invoke_custom('magento:deploy:static');
        invoke_custom('magento:deploy:static:refresh:version');
    }

    if ((int)get("is_production") === 1) {
        invoke_custom('magento:di:compile');
    }

    invoke_custom('magento:cache:flush');

    invoke_custom('deploy:symlink');
    invoke_custom('deploy:actions:after');
    invoke_custom('deploy:unlock');
    invoke_custom('cleanup');
    invoke_custom('success');
});
