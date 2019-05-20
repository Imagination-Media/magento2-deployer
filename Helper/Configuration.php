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

use Deployer\Model\Environment;

class Configuration
{
    const ENV_CONFIG_PATH = "app/etc/deployer/env.json";

    /**
     * @var array
     */
    protected $envConfig;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $projectPath = __DIR__;
        $projectPath = str_replace("vendor/imaginationmedia/deployer-magento2/Helper", "", $projectPath);
        $projectPath = str_replace("deployment/Helper", "", $projectPath);
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

    public function getKeepReleases() : int
    {
        return (key_exists("keep_releases", $this->envConfig["project"]))
            ? (int)$this->envConfig["project"]["keep_releases"]
            : 0;
    }
}
