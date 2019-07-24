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
     * @var string
     */
    protected $httpUser;

    /**
     * @var array
     */
    protected $sharedFiles;

    /**
     * @var array
     */
    protected $sharedDirs;

    /**
     * @var array
     */
    protected $writableDirs;

    /**
     * @var array
     */
    protected $clearPaths;

    /**
     * @var string
     */
    protected $slackWebhook;

    /**
     * @var string
     */
    protected $slackText;

    /**
     * @var string
     */
    protected $slackSuccessText;

    /**
     * @var string
     */
    protected $slackFailureText;

    /**
     * @var array
     */
    protected $ignoredSteps;

    /**
     * @var array
     */
    protected $actionsBeforeSymlinkChange;

    /**
     * @var bool
     */
    protected $composerIgnoreRequirements;

    /***
     * @var bool
     */
    protected $isSymlinkFullpath;

    /**
     * @var array
     */
    protected $themes;

    const DEFAULT_SHARED_FILES = [
        'app/etc/env.php',
        'var/.maintenance.ip'
    ];

    const DEFAULT_SHARED_DIRS = [
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
        'pub/media',
        'pub/static'
    ];

    const DEFAULT_CLEAR_PATHS = [
        'pub/static',
        'var/cache',
        'var/page_cache',
        'var/view_preprocessed',
        'generated'
    ];

    const DEFAULT_WRITABLE_DIRS = [
        'var',
        'pub/static',
        'pub/media',
        'generation'
    ];

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
        $this->beforeCommands = (isset($environmentData["additional_commands"]["before_deploy"]))
            ? $environmentData["additional_commands"]["before_deploy"] : [];
        $this->afterCommands = (isset($environmentData["additional_commands"]["after_deploy"]))
            ? $environmentData["additional_commands"]["after_deploy"] : [];
        $this->keepReleases = (isset($environmentData["keep_releases"]))
            ? (int)$environmentData["keep_releases"] : 3;
        $this->httpUser = (isset($environmentData["http_user"])) ? $environmentData["http_user"] : "";
        $this->sharedFiles = array_unique(
            array_merge(self::DEFAULT_SHARED_FILES,
                (isset($environmentData["shared_files"])) ? $environmentData["shared_files"] : []
            )
        );
        $this->sharedDirs = array_unique(
            array_merge(self::DEFAULT_SHARED_DIRS,
                (isset($environmentData["shared_dirs"])) ? $environmentData["shared_dirs"] : []
            )
        );
        $this->writableDirs = array_unique(array_merge(self::DEFAULT_WRITABLE_DIRS,
            (isset($environmentData["writable_dirs"])) ? $environmentData["writable_dirs"] : []
            )
        );
        $this->clearPaths = array_unique(array_merge(self::DEFAULT_CLEAR_PATHS,
            (isset($environmentData["clear_paths"])) ? $environmentData["clear_paths"] : []
            )
        );
        $this->slackWebhook = isset($environmentData["slack_webhook"])
            ? (is_string($environmentData["slack_webhook"])
                ? [$environmentData["slack_webhook"]]
                : $environmentData["slack_webhook"]
            ) : [];
        $this->slackText = isset($environmentData["slack_text"])
            ? $environmentData["slack_text"]
            : "ATTENTION! User _{{user}}_ is deploying the branch `{{branch}}` to *{{target}}* environment.";
        $this->slackSuccessText = isset($environmentData["slack_success_text"])
            ? $environmentData["slack_success_text"]
            : "*{{target}}* was deployed without any error.";
        $this->slackFailureText = isset($environmentData["slack_failure_text"])
            ? $environmentData["slack_failure_text"]
            : "An error happened deploying `{{branch}}` to *{{target}}* environment.";
        $this->ignoredSteps = isset($environmentData["ignored_steps"])
            ? $environmentData["ignored_steps"]
            : [];
        $this->actionsBeforeSymlinkChange = isset($environmentData["additional_commands"]["before_symlink_change"])
            ? $environmentData["additional_commands"]["before_symlink_change"]
            : [];
        $this->composerIgnoreRequirements = isset($environmentData["composer_ignore_requirements"])
            ? (bool)$environmentData["composer_ignore_requirements"] : false;
        $this->themes = (isset($environmentData["themes"]) && $environmentData["themes"] !== "")
            ? $environmentData["themes"] : [];
        $this->isSymlinkFullpath = (isset($environmentData["symlink_fullpath"]) && $environmentData["symlink_fullpath"] !== "")
            ? (bool)$environmentData["symlink_fullpath"] : false;
    }

    /**
     * @return string
     */
    public function getHostName(): string
    {
        return $this->hostName;
    }

    /**
     * @param string $hostName
     */
    public function setHostName(string $hostName)
    {
        $this->hostName = $hostName;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
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
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getDeployPath(): string
    {
        return $this->deployPath;
    }

    /**
     * @param string $deployPath
     */
    public function setDeployPath(string $deployPath)
    {
        $this->deployPath = $deployPath;
    }

    /**
     * @return string
     */
    public function getBranch(): string
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch(string $branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getIsProduction(): int
    {
        return $this->isProduction;
    }

    /**
     * @param int $isProduction
     */
    public function setIsProduction(int $isProduction)
    {
        $this->isProduction = $isProduction;
    }

    /**
     * @return mixed
     */
    public function getIdentityFile(): string
    {
        return $this->identityFile;
    }

    /**
     * @param string $identityFile
     */
    public function setIdentityFile(string $identityFile)
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
    public function setPhpPath(string $phpPath)
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
    public function setComposerPath(string $composerPath)
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
    public function setKeepReleases(int $keepReleases)
    {
        $this->keepReleases = $keepReleases;
    }

    /**
     * @return string
     */
    public function getHttpUser() : string
    {
        return $this->httpUser;
    }

    /**
     * @param string $httpUser
     */
    public function setHttpUser(string $httpUser)
    {
        $this->httpUser = $httpUser;
    }

    /**
     * @return array
     */
    public function getSharedFiles() : array
    {
        return $this->sharedFiles;
    }

    /**
     * @param array $sharedFiles
     */
    public function setSharedFiles(array $sharedFiles)
    {
        $this->sharedFiles = $sharedFiles;
    }

    /**
     * @return array
     */
    public function getSharedDirs() : array
    {
        return $this->sharedDirs;
    }

    /**
     * @param array $sharedDirs
     */
    public function setSharedDirs(array $sharedDirs)
    {
        $this->sharedDirs = $sharedDirs;
    }

    /**
     * @return array
     */
    public function getWritableDirs() : array
    {
        return $this->writableDirs;
    }

    /**
     * @param array $writableDirs
     */
    public function setWritableDirs(array $writableDirs)
    {
        $this->writableDirs = $writableDirs;
    }

    /**
     * @return array
     */
    public function getClearPaths() : array
    {
        return $this->clearPaths;
    }

    /**
     * @param array $clearPaths
     */
    public function setClearPaths(array $clearPaths)
    {
        $this->clearPaths = $clearPaths;
    }

    /**
     * @return array
     */
    public function getSlackWebhook() : array
    {
        return $this->slackWebhook;
    }

    /**
     * @param array $slackWebhook
     */
    public function setSlackWebhook(array $slackWebhook)
    {
        $this->slackWebhook = $slackWebhook;
    }

    /**
     * @return string
     */
    public function getSlackText() : string
    {
        return $this->slackText;
    }

    /**
     * @param string $slackText
     */
    public function setSlackText(string $slackText)
    {
        $this->slackText = $slackText;
    }

    /**
     * @return string
     */
    public function getSlackSuccessText() : string
    {
        return $this->slackSuccessText;
    }

    /**
     * @param string $slackSuccessText
     */
    public function setSlackSuccessText(string $slackSuccessText)
    {
        $this->slackSuccessText = $slackSuccessText;
    }

    /**
     * @return string
     */
    public function getSlackFailureText() : string
    {
        return $this->slackFailureText;
    }

    /**
     * @param string $slackFailureText
     */
    public function setSlackFailureText(string $slackFailureText)
    {
        $this->slackFailureText = $slackFailureText;
    }

    /**
     * @return array
     */
    public function getIgnoredSteps(): array
    {
        return $this->ignoredSteps;
    }

    /**
     * @param array $ignoredSteps
     */
    public function setIgnoredSteps(array $ignoredSteps)
    {
        $this->ignoredSteps = $ignoredSteps;
    }

    /**
     * @return array
     */
    public function getActionsBeforeSymlinkChange(): array
    {
        return $this->actionsBeforeSymlinkChange;
    }

    /**
     * @param array $actionsBeforeSymlinkChange
     */
    public function setActionsBeforeSymlinkChange(array $actionsBeforeSymlinkChange)
    {
        $this->actionsBeforeSymlinkChange = $actionsBeforeSymlinkChange;
    }

    /**
     * @return bool
     */
    public function isComposerIgnoreRequirements(): bool
    {
        return $this->composerIgnoreRequirements;
    }

    /**
     * @param bool $composerIgnoreRequirements
     */
    public function setComposerIgnoreRequirements(bool $composerIgnoreRequirements)
    {
        $this->composerIgnoreRequirements = $composerIgnoreRequirements;
    }

    /**
     * @return array
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * @param array $themes
     */
    public function setThemes(array $themes)
    {
        $this->themes = $themes;
    }

    /**
     * @return bool
     */
    public function isSymlinkFullpath(): bool
    {
        return $this->isSymlinkFullpath;
    }

    /**
     * @param bool $isSymlinkFullpath
     */
    public function setIsSymlinkFullpath(bool $isSymlinkFullpath)
    {
        $this->isSymlinkFullpath = $isSymlinkFullpath;
    }
}
