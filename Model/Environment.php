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
        $this->slackWebhook = isset($environmentData["slack_webhook"]) ? $environmentData["slack_webhook"] : "";
        $this->slackText = isset($environmentData["slack_text"])
            ? $environmentData["slack_text"]
            : "ATTENTION! User _{{user}}_ is deploying the branch `{{branch}}` to *{{target}}* environment.";
        $this->slackSuccessText = isset($environmentData["slack_success_text"])
            ? $environmentData["slack_success_text"]
            : "*{{target}}* was deployed without any error.";
        $this->slackFailureText = isset($environmentData["slack_failure_text"])
            ? $environmentData["slack_failure_text"]
            : "";
        $this->ignoredSteps = isset($environmentData["ignored_steps"])
            ? $environmentData["ignored_steps"]
            : [];
        $this->actionsBeforeSymlinkChange = isset($environmentData["additional_commands"]["before_symlink_change"])
            ? $environmentData["additional_commands"]["before_symlink_change"]
            : [];
    }

    /**
     * @return mixed
     */
    public function getHostName(): string
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
    public function getUser(): string
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
     * @return mixed
     */
    public function getPort(): string
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
    public function getDeployPath(): string
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
    public function getBranch(): string
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
    public function getIsProduction(): int
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
    public function getIdentityFile(): string
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
    public function getHttpUser()
    {
        return $this->httpUser;
    }

    /**
     * @param string $httpUser
     */
    public function setHttpUser($httpUser)
    {
        $this->httpUser = $httpUser;
    }

    /**
     * @return array
     */
    public function getSharedFiles()
    {
        return $this->sharedFiles;
    }

    /**
     * @param array $sharedFiles
     */
    public function setSharedFiles($sharedFiles)
    {
        $this->sharedFiles = $sharedFiles;
    }

    /**
     * @return array
     */
    public function getSharedDirs()
    {
        return $this->sharedDirs;
    }

    /**
     * @param array $sharedDirs
     */
    public function setSharedDirs($sharedDirs)
    {
        $this->sharedDirs = $sharedDirs;
    }

    /**
     * @return array
     */
    public function getWritableDirs()
    {
        return $this->writableDirs;
    }

    /**
     * @param array $writableDirs
     */
    public function setWritableDirs($writableDirs)
    {
        $this->writableDirs = $writableDirs;
    }

    /**
     * @return array
     */
    public function getClearPaths()
    {
        return $this->clearPaths;
    }

    /**
     * @param array $clearPaths
     */
    public function setClearPaths($clearPaths)
    {
        $this->clearPaths = $clearPaths;
    }

    /**
     * @return string
     */
    public function getSlackWebhook()
    {
        return $this->slackWebhook;
    }

    /**
     * @param string $slackWebhook
     */
    public function setSlackWebhook($slackWebhook)
    {
        $this->slackWebhook = $slackWebhook;
    }

    /**
     * @return string
     */
    public function getSlackText()
    {
        return $this->slackText;
    }

    /**
     * @param string $slackText
     */
    public function setSlackText($slackText)
    {
        $this->slackText = $slackText;
    }

    /**
     * @return string
     */
    public function getSlackSuccessText()
    {
        return $this->slackSuccessText;
    }

    /**
     * @param string $slackSuccessText
     */
    public function setSlackSuccessText($slackSuccessText)
    {
        $this->slackSuccessText = $slackSuccessText;
    }

    /**
     * @return string
     */
    public function getSlackFailureText()
    {
        return $this->slackFailureText;
    }

    /**
     * @param string $slackFailureText
     */
    public function setSlackFailureText($slackFailureText)
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
}
