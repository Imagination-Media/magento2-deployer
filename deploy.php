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
use Deployer\Task\Context;
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
 * =============================== GLOB function to find files including subdirectories ========================
 */
if ( ! function_exists('glob_recursive'))
{
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

/**
 * ============================================== CONFIGURE DEFAULT SETTINGS ===================================
 */

$configuration = new Configuration();

set('application', $configuration->getApplicationName());

set('git_tty', $configuration->getGitTty());

set('repository', $configuration->getRepositoryPath());

set('is_composer_installation', false);

set('allow_anonymous_stats', false);

set('writable_use_sudo', false);

option('mode', null, InputOption::VALUE_OPTIONAL, 'Set the deployment mode.');

/**
 * Set the last commit hash as the release name
 */
set('release_name', function () {
    return run('(git ls-remote {{repository}} | awk "/{{branch}}/ {print $1}") | cut -c1-8');
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
        ->set('ignored_steps', $environment->getIgnoredSteps())
        ->identityFile($environment->getIdentityFile())
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no');
}

/**
 *
 * ========================================= DEPLOYMENT ACTIONS ============================================
 *
 */
task('deploy', function () {
    if (input()->hasOption('mode') &&
        (string)input()->getOption('mode') === "full") {
        writeln("➤ Deploying using FULL mode");
        invoke_custom('deploy:full:actions');
    } else {
        writeln("➤ Deploying using UPDATE mode");
        invoke_custom('deploy:update:actions');
    }
});

/**
 * ========================================== Set SLACK options ============================================
 */

set('slack_color', "#15bb3c");
set('slack_failure_color', "#fa1212");

task('before:deploy:slack:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke_custom('slack:notify');
    }
});

task('deploy:slack:success:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke_custom('slack:notify:success');
    }
});

task('deploy:slack:failed:notify', function () {
    if ((string)get("slack_webhook") !== "") {
        invoke_custom('slack:notify:failure');
    }
});

before('deploy', 'before:deploy:slack:notify');
after('success', 'deploy:slack:success:notify');
after('deploy:failed', 'deploy:slack:failed:notify');