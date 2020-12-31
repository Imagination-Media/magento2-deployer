

# Magento 2 Deployment Tool  

Magento 2 code deployment tool with automated steps and extensibility features.  
  
How to install  
-------  
  
First you have to install [Deployer](https://deployer.org/):  
  
```  
curl -LO https://deployer.org/releases/v6.6.0/deployer.phar && mv deployer.phar /usr/local/bin/dep && chmod +x /usr/local/bin/dep  
```  
  
How to install this **Magento 2 Deployment Tool**:  
  
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

On this config file you have two sections; project and environments.

On project we set:

-   **project_name:** The project name
-   **repository_path:** The git path to the repository
-   **git_tty:** Allocate TTY for `git clone` command. **`false`** **by default.**  This allow you to enter a passphrase for keys or add host to `known_hosts`.

On environments we will set each environment that we are able to deploy. For each environment you can set:

-   **hostname:**  the host ip address.
-   **user:**  the user that we are going to use to deploy.
-   **port:**  the port used to access using the ssh user.
-   **deploy_path:**  what is the path our deployment tool is going to use to create the releases folder, the current symlink and all the required directories/files.
-   **branch:**  what is the branch we are going to deploy.
-   **is_production:**  a flag where we set if Magento is going to use production or developer mode. If we set as production mode additional commands will be executed (deploy static content and compile code)
-   **identity_file:**  path in the server where deployer will get the ssh key allowed to access our git repository (the store repo).
-   **languages:**  set the languages available in your Magento store (for stores with multiple languages). You can setup all the languages like: en_US, pt_BR, it_IT.
-   **php_path:**  The path to the installed PHP that will be used to run all the Magento cli commands.
-   **composer_path:**  The path to the installed composer.
-   **keep_release:**  Set how many releases we will keep in the environment.
-   **http_user:**  There is a step in our deployment process that we set the owner of all shared files and directories. This http_user setting allows us to set the host user that will own these files/directories.  
-   **slack_webhook:**  in case you need to notify a slack channel about the deployments you can set here the slack webhook url used to send these notifications.
-   **additional_commands:**  is a config where we can define two values; before_deploy and after_deploy. It's useful to setup additional commands that we need to run before or after the deployment starts/finish. For example if you need to restart apache after the deployment finishes, you can add the command inside the after_deploy value.
    -   **before_deploy:**  we can set the commands that will be executed in the server before we start the deployment process.
    -   **after_deploy:**  we can set the commands that will be executed in the server after the deployment is finished.
    -   **before_symlink_change**: we can set the commands that will be executed before we change the symlink.
-   **shared_files:**  Here we set what are the shared files that we have to create the symlinks. By default we are creating symlinks for these files: app/etc/env.php and var/.maintenance.ip. Any new path added to this config will be merged with the default values.
-   **shared_dirs:** Here we set what are the shared directories that we have to create the symlinks. By default we are creating symlinks for these directories: 'var/composer_home', 'var/log', 'var/cache', 'var/export', 'var/report', 'var/import_history', 'var/session', 'var/importexport', 'var/backups', 'var/tmp', 'pub/sitemaps', 'pub/media', 'pub/static'. Any new path added to this config will be merged with the default values.
-   **writable_dirs:**  Here we set the directories that we will change the owner and set it as writable. By default these are the directories: 'var', 'pub/static', 'pub/media', 'generation'. Any new path added to this config will be merged with the default values.
-   **clear_paths:**  We set on this config the directories that we will clean after every deployment. By default they are 'pub/static', 'var/cache', 'var/page_cache', 'var/view_preprocessed', 'generated'. Any new path added to this config will be merged with the default values.
-   **slack_text:**  You can use this setting to setup the message used to notify slack about a new deployment. By default it's ATTENTION! User _{{user}}_ is deploying the branch `{{branch}}` to *{{target}}* environment.
-   **slack_success_text:** You can use this setting to setup the message used to notify slack about a success deployment. By default it's *{{target}}* was deployed without any error.
-   **slack_failure_text:**  You can use this setting to setup the message used to notify slack about a failed deployment.
-   **composer_ignore_requirements:**  You can set this boolean flag if you need to ignore platform requirements during composer install.
-   **themes:**  On this setting you can setup the themes that you would like to deploy. All the other themes will be ignored.
-   **symlink_fullpath:** This option will create all the symlinks for files and directories using the full path. If this option is set as false the path will be  **../../shared/var/log (relative path)**, if this option is set as true it will be  **/srv/user/shared/var/log (full path)**.

You don't need to keep this file in your repo, but you need to have it if you want to run the deployment commands from your machine (we will show how to do that).

Deployer will create this structure in the server:

![Deployer directory structure](https://image.prntscr.com/image/LLEqrglZQ-SRu1qU1key9A.png)  

**.dep** is the folder used by deployer to manage the deployer release and all the recent changes.

**releases**  is the folder where we deploy all the new releases. It keep a quantity of release that we defined in the **keep_release** setting.

![Deployer releases](https://i.ibb.co/NYmhfLy/SHARED1.png)

**shared**  is the folder where we store all the shared files and directories that we create symlinks to the current release. An example is the pub/media directory.

![Deployer shared](https://i.ibb.co/12132z4/shared2.png)

**current**  is a symlink to the latest release. Our server webroot should point to this  **symlink/pub.** So the webroot will be  **current/pub.**

Deploying manually & dep commands
----- 

If you want to manually run the commands you have to install the deployer tool using composer, or download the package and uncompress in a directory named as **deployment**.

Now that you are able to clone the git code from the repository we can deploy it using the  **dep commands**.

Inside your local project you have to access the deployer tool directory (deployment if you installed manually, or vendor/imaginationmedia/deployer-magento2 if you installed by composer).

You are able to run these commands:

-   **dep deploy environmentName**  - It will deploy an environment. For example **dep deploy staging**. You can also specify the verbose (-v, -vv, -vvv) to show all the logs.
-   **dep deploy:unlock environmentName**  - It will unlock the deployment process for this environment, in case it's locked. Every time we run a new deployment, our tool locks the environment, to avoid multiple deployments at same time. In case of a failed deployment, it will stay locked. So we need to run this command to unlock. An example is  **dep deploy:unlock environmentName**.
-   **dep rollback environmentName**  - In case of any error in the new release, we can quickly switch back to the previous release, using this command. An example is  **dep rollback staging.**
-   **dep ssh environmentName** - You can access ssh using this command. An example is **dep ssh staging.**

**Keep in my mind that you need to have the app/etc/deployer/env.json in your local to be able to run these commands.**


CI/CD with BitBucket Pipelines
-----
Bitbucket Pipelines is a powerful tool that can be used to automate deployments. Let's say you have 2 branches, staging and master. Staging is used to deploy code to a test environment, and master is used to deploy the final code to production. We can use Bitbucket pipelines to deploy code to these environments every time a new commit is pushed to these branches.

We have also created a docker image that can be used to automate these deployments. Our docker image is available in [DockerHub.](https://hub.docker.com/r/igorimaginemage/php71)

In order to start to set up Bitbucket pipelines to automate deployment you need to create a file called **bitbucket-pipelines.yml** in your repo, and you can add this content:

```
image: igorimaginemage/php71
pipelines:
  branches:
    master:
      - step:
          name: Deploy Production
          deployment: production
          size: 2x
          caches:
            - composer
          script:
            - cp /opt/atlassian/pipelines/agent/data/id_rsa /root/.ssh/id_rsa
            - curl -LO https://github.com/Imagination-Media/magento2-deployer/raw/master/deployer/releases/6.6.0/dep && mv dep /usr/local/bin/dep && chmod +x /usr/local/bin/dep
            - mkdir -p app/etc/deployer
            - sh -c 'echo "$DEPLOYER_ENV" >> app/etc/deployer/env.json'
            - git clone https://github.com/deployphp/distribution.git vendor/deployer/dist
            - git clone https://github.com/deployphp/recipes.git vendor/deployer/recipes
            - git clone https://github.com/Imagination-Media/magento2-deployer.git vendor/imaginationmedia/deployer-magento2
            - deployerUser=$(git log master -1 --pretty=format:'%an') && git config user.name "$deployerUser"
            - cd vendor/imaginationmedia/deployer-magento2/ && dep deploy production -vvv
    staging:
      - step:
          name: Deploy Staging Environment
          deployment: staging
          caches:
            - composer
          script:
            - cp /opt/atlassian/pipelines/agent/data/id_rsa /root/.ssh/id_rsa
            - curl -LO https://github.com/Imagination-Media/magento2-deployer/raw/master/deployer/releases/6.6.0/dep && mv dep /usr/local/bin/dep && chmod +x /usr/local/bin/dep
            - mkdir -p app/etc/deployer
            - sh -c 'echo "$DEPLOYER_ENV" >> app/etc/deployer/env.json'
            - git clone https://github.com/deployphp/distribution.git vendor/deployer/dist
            - git clone https://github.com/deployphp/recipes.git vendor/deployer/recipes
            - git clone https://github.com/Imagination-Media/magento2-deployer.git vendor/imaginationmedia/deployer-magento2
            - deployerUser=$(git log staging -1 --pretty=format:'%an') && git config user.name "$deployerUser"
            - cd vendor/imaginationmedia/deployer-magento2/ && dep deploy staging -vvv
```

As you can see, it uses our docker image to build the cloud machine and then it basically clone our deployment tool to this remote machine and executes the dep deploy command.

We have 2 branches being watched, they are master and staging. On each one we run the proper deployment command (dep deploy staging for staging and dep deploy production for production).

If your server is restricting ssh access by ip address you will also need to whitelist bitbucket cloud ips. They are **34.199.54.113/32, 34.232.25.90/32, 34.232.119.183/32, 34.236.25.177/32, 35.171.175.212/32, 52.54.90.98/32, 52.202.195.162/32, 52.203.14.55/32, 52.204.96.37/32, 34.218.156.209/32, 34.218.168.212/32, 52.41.219.63/32, 35.155.178.254/32, 35.160.177.10/32, 34.216.18.129/32**

Now you need to setup the Bitbucket pipelines repository variables. To do that go to Settings > Pipelines > Repository Variables. The only variable that we need here will be a variable called **DEPLOYER_ENV**. If you check our bitbucket-pipelines.yml file you will see we are creating the app/etc/deployer/env.json with the content of a system variable, our **DEPLOYER_ENV** variable. So, let's create this variable and paste the content of our app/etc/deployer/env.json file.

![Bitbucket Pipelines env variables](https://i.ibb.co/GHRS5pc/test1.png)

The last and not less important step, Bitbucket pipelines will ssh into your server, so, it has to be authenticated without any password? What does it means? We will need a ssh key for bitbucket pipelines. That's easy, go to Settings > Pipelines > SSH Keys and generate a new key pair. Then whitelist the public key in your server and you are ready to go.

![Bitbucket Key Pair Generation](https://i.ibb.co/d7P97P1/print3.png)

Now you are ready to run automate deployments using bitbucket pipelines!

![Bitbucket Pipelines deployment](https://i.ibb.co/cbbdn1n/print4.png)

Recommend Host Solutions
-----
We have tested this deployment solution with different host providers, but here are our preferences and our partners where the process works better.

## ![JetRails Logo](https://jetrails.com/wp-content/uploads/2018/01/logo.svg)

JetRails hosting for Magento is focused on the five pillars of mission critical hosting: Speed, Security, Support, Scalability, and Stability. Their platform continuously integrates the latest advancements to ensure the best hosting experience.

[Website](https://jetrails.com/)

### Setup deployer on Jetrails

You will need to basically follow all the steps that were described above, and in the **before_symlink_change** config you will have to provide these three lines:

```json
"before_symlink_change" : [  
  "cd {{release_path}} && find ./ -type f -exec chmod -c 664 {} +",  
  "cd {{release_path}} && find ./ -type d -exec chmod -c 775 {} +",  
  "{{php}} bin/magento cache:flush"  
]
```

You will also need to open a support ticket requesting the Bitbucket Pipelines ips to be whitelisted in your server in case you want to use bitbucket pipelines for CI/CD.

#### Cron configuration
On JR we need to disable the cron jobs and start them again after every new deployment in order to avoid duplicated cron jobs os jobs running on incorrect code releases. We can't stop and start the cron service like we can do on AWS or MageMojo. So, in order to do that you first need to clean the crontab before a new deployment. On the **before_deploy** step you need to add the **"crontab -r"** command, it will clear the crontab, completely.

```
"before_deploy":[
  "crontab -r"
]
```

Then on the **after_deploy** step you need to add the Magento cron:install command. This command will setup the Magento crontab for you.

```
"after_deploy":[  
  "/usr/bin/php7.4 /home/jetrails/mystore.com/html/bin/magento cron:install"  
]
```

It will add something to the crontab like:

```
#~ MAGENTO START aasasasasa4s5a4s5a4s5a4s5187281728121212
* * * * * /usr/bin/php7.4 /home/jetrails/mystore.com/releases/20201228142353/bin/magento cron:run 2>&1 | grep -v "Ran jobs by schedule" >> /home/jetrails/mystore.com/releases/20201228142353/var/log/magento.cron.log
#~ MAGENTO END aasasasasa4s5a4s5a4s5a4s5187281728121212
```

In case you have any other custom cron job that you need to add to the crontab, you can also add it to the crontab using the after_deploy step. For example:

```
"after_deploy":[  
  "/usr/bin/php7.4 /home/jetrails/mystore.com/html/bin/magento cron:install",
  "(crontab -l ; echo "#my command")| crontab -" 
],
```

**#my command** is the line you want to add to the crontab.

**Also, please don't enable the cron for staging environments, otherwise they will dump the production configuration every time we deploy to staging. Enable CRON on staging only when needed for a test and manually. Don't add to the deployment script.**

After a new production deployment make sure you check the cron_schedule table to see if any cron job got stuck during the deployment process.

You should have no downtime during this process.
##  ![MageMojo Stratus Logo](https://res-1.cloudinary.com/crunchbase-production/image/upload/c_lpad,h_256,w_256,f_auto,q_auto:eco/tgc7ph5dux6kpcqnwfua)

MageMojo Stratus created a unique hosting offering specifically and solely aimed at optimized Magento hosting.  
  
Over ten years later, MageMojo is still growing. By delivering the highest level of Magento performance at prices no one can match in an apples-to-apples comparison, MageMojo established itself as the leader in its niche. And it’s achieved this growth by keeping its customers satisfied, with the industry’s only 15-minute support SLA.

[Website](https://magemojo.com/)

### Setup deployer on MageMojo Stratus

You will need to basically follow all the steps that were described above and the option **symlink_fullpath** in your app/etc/deployer/env.json config has to be true ([https://magemojo.com/kb/stratus-best-practices/using-symlinks/](https://magemojo.com/kb/stratus-best-practices/using-symlinks/)).

You also need to add the MM stratus clear cache command to be executed before a new deployment, on the before_deploy step:

```json
"before_deploy": [
  "/usr/share/stratus/cli cache.all.clear"
]
```

First of all, before you start a new deployment you have to stop the Magento cron, to avoid a cron job to get stuck during the deployment process. You can do that on the MM Stratus panel.

![MM Stratus Cron](https://i.ibb.co/DpbR8cL/mm-cron1.png)

Then you can start the deployment.

If you have the auto scaling option enabled, once the deployment is complete you have to re-init the auto scaling. You can also do that on MM stratus panel.

![MM Stratus Auto scaling](https://i.ibb.co/bXNms8F/mm-autoscaling.png)

That's the only time the store goes down for few seconds, because the servers are restarting.

Then, once your store is back you need to enable the cron again.

Keep checking on the next hours if any cron job get stuck on the cron_schedule table.

To use bitbucket pipelines for CI\CD you will also need to whitelist Bitbucket Cloud ips on the MM Stratus panel.
