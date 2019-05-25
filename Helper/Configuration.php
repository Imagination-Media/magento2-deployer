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

namespace Deployer\Helper;

use function Deployer\glob_recursive;
use Deployer\Model\Environment;

class Configuration
{
    const ENV_CONFIG_PATH = "app/etc/deployer/env.json";

    /**
     * @var array
     */
    protected $envConfig;

    /**
     * @var string
     */
    protected $projectRoot;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $projectPath = __DIR__;
        $projectPath = str_replace("vendor/imaginationmedia/deployer-magento2/Helper", "", $projectPath);
        $projectPath = str_replace("deployment/Helper", "", $projectPath);
        $this->projectRoot = $projectPath;
        $jsonFile = file_get_contents($projectPath . self::ENV_CONFIG_PATH);
        $this->envConfig = json_decode($jsonFile, true);
    }

    /**
     * Get project name
     * @return string
     */
    public function getApplicationName() : string
    {
        return (key_exists("project_name", $this->envConfig["project"]))
            ? (string)$this->envConfig["project"]["project_name"]
            : "";
    }

    /**
     * Get repository path
     * @return string
     */
    public function getRepositoryPath() : string
    {
        return (key_exists("repository_path", $this->envConfig["project"]))
            ? (string)$this->envConfig["project"]["repository_path"]
            : "";
    }

    /**
     * Use git tty
     * @return bool
     */
    public function getGitTty() : bool
    {
        return (key_exists("git_tty", $this->envConfig["project"]))
            ? (bool)$this->envConfig["project"]["git_tty"]
            : false;
    }

    /**
     * Get all environments
     * @return array
     */
    public function getEnvironments() : array
    {
        $finalEnvs = [];
        if (key_exists("environments", $this->envConfig) && is_array($this->envConfig["environments"])) {
            $envs = $this->envConfig["environments"];
        } else {
            $envs = [];
        }

        foreach ($envs as $name => $env) {
            $environment = new Environment($env);
            $finalEnvs[$name] = $environment;
        }

        return $finalEnvs;
    }

    /**
     * Get keep releases quantity
     * @return int
     */
    public function getKeepReleases() : int
    {
        return (key_exists("keep_releases", $this->envConfig["project"]))
            ? (int)$this->envConfig["project"]["keep_releases"]
            : 0;
    }

    /**
     * Is this project using the composer package
     * @return int
     */
    public function isComposerInstallation() : int
    {
        return (key_exists("is_composer_installation", $this->envConfig["project"]))
            ? (int)$this->envConfig["project"]["is_composer_installation"]
            : 0;
    }

    /**
     * Is the command setup:upgrade necessary?
     * @return bool
     */
    public function isSetupUpgradeNecessary() : bool
    {
        /**
         * =================== Check installed packages ====================
         */
        $appCodeItems = glob_recursive($this->projectRoot . "app/code/module.xml");
        $vendorItems = glob_recursive($this->projectRoot . "vendor/module.xml");
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
            $installedPackages[$value['@attributes']['name']] = $value['@attributes']['setup_version'];
        }

        /**
         * ======================== Get modules enabled on etc/config.php ============================
         */
        $configModules = include $this->projectRoot . '/app/etc/config.php';
        $configModules = $configModules['modules'];

        /**
         * Remove non enabled modules from installedPackages variable
         * and check for items that are not in app/etc/config.php file
         */
        foreach ($installedPackages as $installedKey => $installedPackage) {
            if (key_exists($installedKey, $configModules) &&
                (int)$configModules[$installedKey] === 0) {
                unset($installedPackages[$installedKey]);
            }/* elseif (!key_exists($installedKey, $configModules)) {
                return true; //So we have to run setup:upgrade to install the new module
            }*/
        }

        /**
         * ======================== Get installed version from the database ==========================
         */
        $databaseSchema = file_get_contents($this->projectRoot . "/var/db_schema.json");
        $databaseSchema = json_decode($databaseSchema, true);

        foreach ($installedPackages as $installedName => $installedVersion) {
            foreach ($databaseSchema as $schemaModuleName => $schemaModuleVersion) {
                if ($installedName === $schemaModuleName && $installedVersion !== $schemaModuleVersion) {
                    return true;
                }
            }
        }

        return false;
    }
}
