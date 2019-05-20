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

task('deploy:full:actions', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:actions:before',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'magento:maintenance:enable',
    'composer:install',
    'magento:mode:production',
    'magento:upgrade',
    'magento:deploy:static',
    'magento:di:compile',
    'magento:maintenance:disable',
    'deploy:symlink',
    'deploy:actions:after',
    'deploy:unlock',
    'cleanup',
    'success'
]);
