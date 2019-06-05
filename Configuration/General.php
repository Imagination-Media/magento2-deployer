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

/**
 * ================================= MAGENTO COMMANDS ====================================
 */
task('magento:maintenance:enable', function () {
    run("cd {{release_path}} && {{php}} bin/magento maintenance:enable");
});

task('magento:maintenance:disable', function () {
    run("cd {{release_path}} && {{php}} bin/magento maintenance:disable");
});

task('magento:upgrade', function () {
    run("cd {{release_path}} && {{php}} bin/magento setup:upgrade");
});

task('magento:upgrade:keep:generated', function () {
    run("cd {{release_path}} && {{php}} bin/magento setup:upgrade --keep-generated");
});

task('magento:deploy:static', function () {
    run("cd {{release_path}} && {{php}} bin/magento setup:static-content:deploy {{languages}}");
});

task('magento:deploy:static:refresh:version', function () {
    run("cd {{release_path}} && {{php}} bin/magento setup:static-content:deploy --refresh-content-version-only");
});

task('magento:mode:production', function () {
    run("cd {{release_path}} && {{php}} bin/magento deploy:mode:set production --skip-compilation");
});

task('magento:mode:developer', function () {
    run("cd {{release_path}} && {{php}} bin/magento deploy:mode:set developer");
});

task('magento:di:compile', function () {
    run("cd {{release_path}} && {{php}} bin/magento setup:di:compile");
});

task('magento:cache:clean', function () {
    run("cd {{release_path}} && {{php}} bin/magento cache:clean");
});

task('magento:cache:flush', function () {
    run("cd {{release_path}} && {{php}} bin/magento cache:flush");
});

/**
 * ================================== COMPOSER ===========================================
 */
task('composer:install', function () {
    if (get("composer_ignore_requirements")) {
        run("cd {{release_path}} && {{composer}} install --ignore-platform-reqs");
    } else {
        run("cd {{release_path}} && {{composer}} install");
    }
});

/**
 * ================================== BEFORE & AFTER COMMANDS ============================
 */
task('deploy:actions:before', function () {
    $beforeCommands = get("before_commands");
    if (count($beforeCommands) > 0) {
        foreach ($beforeCommands as $beforeCommand) {
            run($beforeCommand);
        }
    }
});

task('deploy:actions:after', function () {
    $afterCommands = get("after_commands");
    if (count($afterCommands) > 0) {
        foreach ($afterCommands as $afterCommand) {
            run("cd {{release_path}} && " . $afterCommand);
        }
    }
});

task('deploy:actions:before:symlink', function () {
    $beforeCommands = get("actions_before_symlink");
    if (count($beforeCommands) > 0) {
        foreach ($beforeCommands as $beforeCommand) {
            run("cd {{release_path}} && " . $beforeCommand);
        }
    }
});

/**
 * ================================== GENERAL COMMANDS ============================================
 */

task('generated:db:schema', function () {
    writeln("➤ Creating the database schema file. It will be available on var/db_schema.json");
    if ((int)get("is_composer_installation") === 1) {
        run("cd {{release_path}} && php vendor/imaginationmedia/deployer-magento2/Helper/Scripts/db_schema_generator.php 1 {{release_path}}");
    } else {
        run("cd {{release_path}} && php deployment/Helper/Scripts/db_schema_generator.php 1 {{release_path}}");
    }
});

/**
 * ================================== GIT COMMANDS =================================================
 */
task('git:update:base:code', function () {
    run("cd {{release_path}} && git reset --hard && git pull origin {{branch}}");
});