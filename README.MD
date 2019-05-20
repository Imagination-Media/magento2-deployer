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
    "project_name": "Imagination Media Innovations Website",
    "repository_path": "git@bitbucket.org:imagination-media/innovations-website-m2.git",
    "git_tty" : false,
    "keep_releases" : 5
  },
  "environments" : {
    "production" : {
      "hostname" : "54.177.43.119",
      "user" : "root",
      "password" : "",
      "port" : "",
      "deploy_path" : "/var/www",
      "branch" : "master",
      "is_production" : 1,
      "identity_file" : "/Users/igorludgeromiura/.ssh/aws-innovate.pem",
      "languages" : "en_US",
      "php_path" : "/usr/bin/php",
      "composer_path" : "/usr/bin/composer",
      "additional_commands" : {
        "before_deploy" : [],
        "after_deploy" : ["sudo service httpd reload"]
      }
    }
  }
}
```
