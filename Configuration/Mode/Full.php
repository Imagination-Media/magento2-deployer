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

require_once 'recipe/common.php';

task('deploy:full:actions', function() {
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:actions:before');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('deploy:shared');
    invoke('deploy:writable');
    invoke('deploy:vendors');
    invoke('deploy:clear_paths');
    invoke('composer:install');
    invoke('magento:maintenance:enable');

    if ((int)get("is_production") === 1) {
        invoke('magento:mode:production');
    } else {
        invoke('magento:mode:developer');
    }

    invoke('magento:upgrade');
    invoke('magento:deploy:static');

    if ((int)get("is_production") === 1) {
        invoke('magento:di:compile');
    }

    invoke('magento:maintenance:disable');
    invoke('deploy:symlink');
    invoke('deploy:actions:after');
    invoke('deploy:unlock');
    invoke('cleanup');
    invoke('success');
});
