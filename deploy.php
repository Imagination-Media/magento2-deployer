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

require '../../deployer/recipes/recipe/slack.php';
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

set('allow_anonymous_stats', false);

set('writable_use_sudo', false);

/**
 * Set the last commit hash as the release name
 */
set('release_name', function () {
    return run('git ls-remote {{repository}} | awk "/{{branch}}/ {print \$1}"');
});

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
        ->set('http_user', $environment->getHttpUser())
        ->set('writable_mode', 'chown')
        ->set('languages', $environment->getLanguages())
        ->set('shared_files', $environment->getSharedFiles())
        ->set('shared_dirs', $environment->getSharedDirs())
        ->set('writable_dirs', $environment->getWritableDirs())
        ->set('clear_paths', $environment->getClearPaths())
        ->set('slack_webhook', $environment->getSlackWebhook())
        ->set('slack_text', $environment->getSlackText())
        ->set('slack_success_text', $environment->getSlackSuccessText())
        ->set('slack_failure_text', $environment->getSlackFailureText())
        ->identityFile($environment->getIdentityFile())
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no');
}

/**
 *
 * ========================================= DEPLOYMENT ACTIONS ============================================
 *
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

/**
 * ================== Set SLACK options ==========================
 */

set('slack_color', "#15bb3c");
set('slack_failure_color', "#fa1212");

task('before:deploy:slack:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke('slack:notify');
    }
});

task('deploy:slack:success:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke('slack:notify:success');
    }
});

task('deploy:slack:failed:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke('slack:notify:failure');
    }
});

before('deploy:full', 'before:deploy:slack:notify');
before('deploy:update', 'before:deploy:slack:notify');
after('success', 'deploy:slack:success:notify');
after('deploy:failed', 'deploy:slack:failed:notify');