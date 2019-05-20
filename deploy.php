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
require_once './Helper/Configuration.php';
require_once './Model/Environment.php';
require_once './Configuration/General.php';
require_once './Configuration/Mode/Full.php';
require_once './Configuration/Mode/Update.php';

use Deployer\Helper\Configuration;

$configuration = new Configuration();

set('application', $configuration->getApplicationName());

set('git_tty', $configuration->getGitTty());

set('repository', $configuration->getRepositoryPath());

set('shared_files', [
    'app/etc/env.php',
    'app/etc/deployer/env.json',
    'var/.maintenance.ip'
]);

set('shared_dirs', [
    'var/composer_home',
    'var/log',
    'var/cache',
    'var/export',
    'var/report',
    'var/import_history',
    'var/session',
    'var/importexport',
    'var/backups',
    'var/tmp',
    'pub/sitemaps',
    'pub/media'
]);

set('writable_dirs', [
    'var',
    'pub/static',
    'pub/media',
    'generation'
]);

set('clear_paths', [
    'pub/static/_cache',
    'var/cache',
    'var/page_cache',
    'var/view_preprocessed',
    'generated'
]);

set('allow_anonymous_stats', false);

set('writable_use_sudo', false);

/**
 * Configure all hosts
 * @var $environment \Deployer\Model\Environment
 */
foreach ($configuration->getEnvironments() as $environmentName => $environment) {
    host($environmentName)
        ->hostname($environment->getHostName())
        ->user($environment->getUser())
        ->port($environment->getPort())
        ->set('deploy_path', $environment->getDeployPath())
        ->set('branch', $environment->getBranch())
        ->set('is_production', $environment->getIsProduction())
        ->set('php', $environment->getPhpPath())
        ->set('composer', $environment->getComposerPath())
        ->set('before_commands', $environment->getBeforeCommands())
        ->set('after_commands', $environment->getAfterCommands())
        ->set('keep_releases', $environment->getKeepReleases())
        ->identityFile($environment->getIdentityFile())
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no');

    if ($environment->getPassword() !== "") {
        host($environmentName)->password($environment->getPassword());
    }
}

/**
 * Run a full deploy, with all actions, rebuilding everything
 */
task('deploy:full', [
    'deploy:full:actions'
]);

/**
 * Update current release, applying recent changes
 */
task('deploy:update', [
    'deploy:update:actions'
]);
