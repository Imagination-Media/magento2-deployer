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

use Deployer\Helper\Configuration;
use Deployer\Task\Context;
use Deployer\Utility\Httpie;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * ====== Use custom invoke function to ignore tasks declared as ignored on env.json ==========================
 */

function invoke_custom($task)
{
    if (!in_array($task, get("ignored_steps"))) {
        $hosts = [Context::get()->getHost()];
        $tasks = Deployer::get()->scriptManager->getTasks($task, $hosts);

        $executor = Deployer::get()->seriesExecutor;
        $executor->run($tasks, $hosts);
    } else {
        writeln("➤ Step ".$task." was ignored because it's set as ignored on env.json file.");
    }
}

/**
 * ============================================== CONFIGURE DEFAULT SETTINGS ===================================
 */

$configuration = new Configuration();

set('application', $configuration->getApplicationName());

set('git_tty', $configuration->getGitTty());

set('repository', $configuration->getRepositoryPath());

if (strpos(__DIR__, 'vendor/imaginationmedia/deployer-magento2') !== false) {
    set('is_composer_installation', true);
} else {
    set('is_composer_installation', false);
}

set('allow_anonymous_stats', false);

set('writable_use_sudo', false);

option('mode', null, InputOption::VALUE_OPTIONAL, 'Set the deployment mode.');

set('release_name', function () {
    return date('YmdHis');
});

set('default_timeout', null);

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
        ->set('ignored_steps', $environment->getIgnoredSteps())
        ->set('composer_ignore_requirements', $environment->isComposerIgnoreRequirements())
        ->set('actions_before_symlink', $environment->getActionsBeforeSymlinkChange())
        ->set('themes', $environment->getThemes())
        ->identityFile($environment->getIdentityFile())
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no');

    //If symlink_fullpath is set as true we will deploy the symlinks using the fullpath
    if ($environment->isSymlinkFullpath()) {
        host($environmentName)
            ->set("bin/symlink", "ln -s");
    }
}

/**
 *
 * ========================================= DEPLOYMENT ACTIONS ============================================
 *
 */
task('deploy', function () {
    invoke_custom('deploy:full:actions');
});

/**
 * ========================================== Set SLACK options ============================================
 */

set('slack_color', "#15bb3c");
set('slack_failure_color', "#fa1212");

desc('Notifying Slack');
task('custom:slack:notify', function () {
    if (!get('slack_webhook', false)) {
        return;
    }
    foreach (get('slack_webhook') as $webhook) {
        $attachment = [
            'title' => get('slack_title'),
            'text' => get('slack_text'),
            'color' => get('slack_color'),
            'mrkdwn_in' => ['text'],
        ];
        Httpie::post($webhook)->body(['attachments' => [$attachment]])->send();
    }
})
    ->once()
    ->shallow()
    ->setPrivate();


desc('Notifying Slack about deploy finish');
task('custom:slack:notify:success', function () {
    if (!get('slack_webhook', false)) {
        return;
    }

    foreach (get('slack_webhook') as $webhook) {
        $attachment = [
            'title' => get('slack_title'),
            'text' => get('slack_success_text'),
            'color' => get('slack_success_color'),
            'mrkdwn_in' => ['text'],
        ];
        Httpie::post($webhook)->body(['attachments' => [$attachment]])->send();
    }
})
    ->once()
    ->shallow()
    ->setPrivate();


desc('Notifying Slack about deploy failure');
task('custom:slack:notify:failure', function () {
    if (!get('slack_webhook', false)) {
        return;
    }

    foreach (get('slack_webhook') as $webhook) {
        $attachment = [
            'title' => get('slack_title'),
            'text' => get('slack_failure_text'),
            'color' => get('slack_failure_color'),
            'mrkdwn_in' => ['text'],
        ];
        Httpie::post($webhook)->body(['attachments' => [$attachment]])->send();
    }
})
    ->once()
    ->shallow()
    ->setPrivate();

task('before:deploy:slack:notify', function () {
    if (count(get("slack_webhook")) > 0) {
        invoke_custom('custom:slack:notify');
    }
});

task('deploy:slack:success:notify', function () {
    if (count(get("slack_webhook")) > 0) {
        invoke_custom('custom:slack:notify:success');
    }
});

task('deploy:slack:failed:notify', function () {
    if (count(get("slack_webhook")) > 0) {
        invoke_custom('custom:slack:notify:failure');
    }
});

before('deploy', 'before:deploy:slack:notify');
after('success', 'deploy:slack:success:notify');
after('deploy:failed', 'deploy:slack:failed:notify');

after('deploy:failed', 'deploy:unlock');
