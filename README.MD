# Deployer Magento 2 recipe
Easy tool to deploy and run automated commands in your Magento 2 server.

How to install
-------

How to install [Deployer](https://deployer.org/):

```
curl -LO https://deployer.org/deployer.phar && sudo mv deployer.phar /usr/local/bin/dep && sudo chmod +x /usr/local/bin/dep
```

How to install this **Magento 2 Deployer Tool**:

```
composer require imaginationmedia/deployer-magento2
```

How to use
-----

Create a file `env.json` inside app/etc/deployer and configure everything about the environments, repository, project name etc.

```json
{
  "project": {
    "project_name": "Project Name",
    "repository_path": "git@bitbucket.org:project/repo.git",
    "git_tty" : false
  },
  "environments" : {
    "staging" : {
      "hostname" : "1.2.3.4",
      "user" : "deploy",
      "port" : "2223",
      "deploy_path" : "/home/path/to/server",
      "branch" : "staging",
      "is_production" : 0,
      "identity_file" : "~/.ssh/id_rsa",
      "languages" : "en_US",
      "php_path" : "/usr/bin/php",
      "composer_path" : "/usr/bin/composer",
      "keep_releases" : 3,
      "http_user" : "deploy",
      "slack_webhook" : "https://hooks.slack.com/services/hook/id",
      "additional_commands" : {
        "before_deploy" : [],
        "after_deploy" : [],
        "actions_before_symlink" : []
      },
      "shared_files" : [],
      "shared_dirs" : [],
      "writable_dirs" : [],
      "clear_paths" : [],
      "ignored_steps" : [],
      "composer_ignore_requirements" : false,
      "themes" : "",
      "symlink_fullpath" : false
    },
    "production" : {
      "hostname" : "1.2.3.4",
      "user" : "deploy",
      "port" : "2223",
      "deploy_path" : "/home/path/to/server",
      "branch" : "staging",
      "is_production" : 1,
      "identity_file" : "~/.ssh/id_rsa",
      "languages" : "en_US",
      "php_path" : "/usr/bin/php",
      "composer_path" : "/usr/bin/composer",
      "keep_releases" : 3,
      "http_user" : "deploy",
      "slack_webhook" : "https://hooks.slack.com/services/hook/id",
      "additional_commands" : {
        "before_deploy" : [],
        "after_deploy" : [],
        "actions_before_symlink" : []
      },
      "shared_files" : [],
      "shared_dirs" : [],
      "writable_dirs" : [],
      "clear_paths" : [],
      "themes" : "",
      symlink_fullpath: false
    }
  }
}
```

For more instructions please access https://imgmedia.atlassian.net/wiki/spaces/IN/pages/585957381/Using+Imagination+Media+deployer+tool+Bitbucket+Pipelines
