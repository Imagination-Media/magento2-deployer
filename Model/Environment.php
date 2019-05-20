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

namespace Deployer\Model;

class Environment
{
    /**
     * @var string
     */
    protected $hostName;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var string
     */
    protected $deployPath;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var int
     */
    protected $isProduction;

    /**
     * @var string
     */
    protected $identityFile;

    /**
     * @var string
     */
    protected $languages;

    /**
     * @var string
     */
    protected $phpPath;

    /**
     * @var string
     */
    protected $composerPath;

    /**
     * @var array
     */
    protected $beforeCommands;

    /**
     * @var array
     */
    protected $afterCommands;

    /**
     * @var int
     */
    protected $keepReleases;

    /**
     * Environment constructor.
     * @param array $environmentData
     */
    public function __construct(array $environmentData)
    {
        $this->hostName = (isset($environmentData["hostname"])) ? $environmentData["hostname"] : "";
        $this->user = (isset($environmentData["user"])) ? $environmentData["user"] : "";
        $this->password = (isset($environmentData["password"])) ? $environmentData["password"] : "";
        $this->port = (isset($environmentData["port"])) ? (int)$environmentData["port"] : 22;
        $this->deployPath = (isset($environmentData["deploy_path"])) ? $environmentData["deploy_path"] : "";
        $this->branch = (isset($environmentData["branch"])) ? $environmentData["branch"] : "";
        $this->isProduction = (isset($environmentData["is_production"])) ? $environmentData["is_production"] : "";
        $this->identityFile = (isset($environmentData["identity_file"])) ? $environmentData["identity_file"] : "";
        $this->languages = (isset($environmentData["languages"])) ? $environmentData["languages"] : "";
        $this->phpPath = (isset($environmentData["php_path"])) ? $environmentData["php_path"] : "";
        $this->composerPath = (isset($environmentData["composer_path"])) ? $environmentData["composer_path"] : "";
        $this->beforeCommands = (isset($environmentData["additional_commands"]["before_deploy"])) ? $environmentData["additional_commands"]["before_deploy"] : [];
        $this->afterCommands = (isset($environmentData["additional_commands"]["after_deploy"])) ? $environmentData["additional_commands"]["after_deploy"] : [];
        $this->keepReleases = (isset($environmentData["keep_releases"]["after_deploy"])) ? (int)$environmentData["additional_commands"]["keep_releases"] : 3;
    }

    /**
     * @return mixed
     */
    public function getHostName() : string
    {
        return $this->hostName;
    }

    /**
     * @param mixed $hostName
     */
    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
    }

    /**
     * @return mixed
     */
    public function getUser() : string
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPort() : string
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getDeployPath() : string
    {
        return $this->deployPath;
    }

    /**
     * @param mixed $deployPath
     */
    public function setDeployPath($deployPath)
    {
        $this->deployPath = $deployPath;
    }

    /**
     * @return mixed
     */
    public function getBranch() : string
    {
        return $this->branch;
    }

    /**
     * @param mixed $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getIsProduction() : int
    {
        return $this->isProduction;
    }

    /**
     * @param mixed $isProduction
     */
    public function setIsProduction($isProduction)
    {
        $this->isProduction = $isProduction;
    }

    /**
     * @return mixed
     */
    public function getIdentityFile() : string
    {
        return $this->identityFile;
    }

    /**
     * @param mixed $identityFile
     */
    public function setIdentityFile($identityFile)
    {
        $this->identityFile = $identityFile;
    }

    /**
     * @return string
     */
    public function getLanguages(): string
    {
        return $this->languages;
    }

    /**
     * @param string $languages
     */
    public function setLanguages(string $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getPhpPath(): string
    {
        return $this->phpPath;
    }

    /**
     * @param string $phpPath
     */
    public function setPhpPath(string $phpPath): void
    {
        $this->phpPath = $phpPath;
    }

    /**
     * @return string
     */
    public function getComposerPath(): string
    {
        return $this->composerPath;
    }

    /**
     * @param string $composerPath
     */
    public function setComposerPath(string $composerPath): void
    {
        $this->composerPath = $composerPath;
    }

    /**
     * @return array
     */
    public function getBeforeCommands(): array
    {
        return $this->beforeCommands;
    }

    /**
     * @param array $beforeCommands
     */
    public function setBeforeCommands(array $beforeCommands)
    {
        $this->beforeCommands = $beforeCommands;
    }

    /**
     * @return array
     */
    public function getAfterCommands(): array
    {
        return $this->afterCommands;
    }

    /**
     * @param array $afterCommands
     */
    public function setAfterCommands(array $afterCommands)
    {
        $this->afterCommands = $afterCommands;
    }

    /**
     * @return int
     */
    public function getKeepReleases(): int
    {
        return $this->keepReleases;
    }

    /**
     * @param int $keepReleases
     */
    public function setKeepReleases(int $keepReleases): void
    {
        $this->keepReleases = $keepReleases;
    }
}
