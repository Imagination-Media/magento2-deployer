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
    $themes = get("themes");
    if (count($themes) > 0) {
        $themeString = "";
        foreach ($themes as $theme) {
            if ($themeString === "") {
                $themeString = "--theme ".$theme;
            } else {
                $themeString .= " --theme ".$theme;
            }
        }
        run("cd {{release_path}} && {{php}} bin/magento setup:static-content:deploy ".$themeString." {{languages}}");
    } else {
        run("cd {{release_path}} && {{php}} bin/magento setup:static-content:deploy {{languages}}");
    }
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
    $composerArguments = '';

    if (get("composer_ignore_requirements")) {
        $composerArguments .= ' --ignore-platform-reqs';
    }

    run("cd {{release_path}} && {{composer}} install $composerArguments");
});

task('composer:dump', function () {
    run("cd {{release_path}} && {{composer}} dump -o");
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
 * ======================================== DEPLOY FAIL ===========================================
 */

desc('Delete invalid release.');
task('delete:invalid:release', function () {
    run("rm -r {{release_path}}");
});
after('deploy:failed', 'delete:invalid:release');

/**
 * ========================================== ROLLBACK ============================================
 */
desc('Steps that are required after a rollback.');
task('rollback:after:actions', function () {
    invoke_custom("magento:maintenance:enable");

    invoke_custom('deploy:actions:before');

    if ((int)get("is_production") === 1) {
        invoke_custom('magento:mode:production');
    } else {
        invoke_custom('magento:mode:developer');
    }

    invoke_custom('magento:upgrade');
    if ((int)get("is_production") === 1) {
        invoke_custom('magento:deploy:static');
    }
    if ((int)get("is_production") === 1) {
        invoke_custom('magento:di:compile');
        invoke_custom('composer:dump');
    }

    invoke_custom('deploy:actions:before:symlink');
    invoke_custom('deploy:actions:after');

    invoke_custom("magento:maintenance:disable");
});
after('rollback', 'rollback:after:actions');