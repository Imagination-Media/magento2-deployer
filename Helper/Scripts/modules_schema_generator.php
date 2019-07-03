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

function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

if (isset($argv[1]) && is_string($argv[1]) && (int)$argv[1] === 1 &&
    isset($argv[2]) && is_string($argv[2]) && $argv[2] !== "") {
    $projectPath = __DIR__;
    $projectPath = str_replace("vendor/imaginationmedia/deployer-magento2/Helper/Scripts", "", $projectPath);
    $projectPath = str_replace("deployment/Helper/Scripts", "", $projectPath);

    /**
     * =================== Check installed packages ====================
     */
    $appCodeItems = glob_recursive($projectPath . "app/code/module.xml");
    $vendorItems = glob_recursive($projectPath . "vendor/module.xml");
    $finalItems = array_merge($appCodeItems, $vendorItems);

    /**
     * Remove dev items
     */
    foreach ($finalItems as $finalItemKey => $finalItemValue) {
        if (stripos($finalItemValue, '/dev/') !== false) {
            unset($finalItems[$finalItemKey]);
        }
    }

    /**
     * Get values from the xml to an array
     */
    $installedPackages = [];
    foreach ($finalItems as $finalItem) {
        $xmlFile = simplexml_load_string(file_get_contents($finalItem));
        $value = json_encode($xmlFile);
        $value = json_decode($value, true);
        $value = $value["module"];
        if (isset($value['@attributes']['setup_version'])) {
            $installedPackages[$value['@attributes']['name']] = $value['@attributes']['setup_version'];
        }
    }

    /**
     * ======================== Get modules enabled on etc/config.php ============================
     */
    $configModules = include $projectPath . '/app/etc/config.php';
    $configModules = $configModules['modules'];

    /**
     * Remove non enabled modules from installedPackages variable
     * and check for items that are not in app/etc/config.php file
     */
    foreach ($installedPackages as $installedKey => $installedPackage) {
        if (key_exists($installedKey, $configModules) &&
            (int)$configModules[$installedKey] === 0) {
            unset($installedPackages[$installedKey]);
        }
    }

    /**
     * Sort order items alphabetically
     */
    ksort($installedPackages);

    $fp = fopen($argv[2] . '/var/db_schema.json', 'w');
    $result = (bool)fwrite($fp, json_encode($installedPackages));
    fclose($fp);

    if (!$result) {
        throw new \Exception('Not possible to generate the database schema file.');
    }
}