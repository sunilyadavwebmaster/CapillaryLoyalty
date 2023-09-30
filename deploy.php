<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'capillary-shopify');

// Project repository
set('repository', 'git@bitbucket.org:spurtreetech/stp_10075_capillary_shopifyapp.git');

set('branch', 'develop');

// [Optional] Allocate tty for git clone. Default value is false.
// set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);

// Hosts
// ->configFile('~/.ssh/config')
host('develop')
	->hostname('cap-shopify-int-dev.spurtreetech.com')
    ->stage('develop')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'develop')
    ->set('deploy_path', '/var/www/shopify_deployment_dev');

host('develop1')
	->hostname('cap-shopify-int-dev1.spurtreetech.com')
    ->stage('develop1')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'dev1')
    ->set('deploy_path', '/var/www/shopify_deployment_dev1');

host('qa')
	->hostname('cap-shopify-int-qa.spurtreetech.com')
    ->stage('qa')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'qa')
    ->set('deploy_path', '/var/www/shopify_deployment_qa'); 

host('uat')
    ->hostname('cap-shopify-int-uat.spurtreetech.com')
    ->stage('uat')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'uat')
    ->set('deploy_path', '/var/www/shopify_deployment_uat');
    
host('uat-redtag')
    ->hostname('cap-shopify-redtag-uat.spurtreetech.com')
    ->stage('uat-redtag')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'uat')
    ->set('deploy_path', '/var/www/shopify_deployment_uat_redtag');

host('uat-customapp')
    ->hostname('cap-shopify-customapp-uat.spurtreetech.com')
    ->stage('uat-customapp')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'uat')
    ->set('deploy_path', '/var/www/shopify_deployment_uat_customapp');

host('prod')
    ->hostname('cap-shopplugin-prod.spurtreetech.com')
    ->stage('prod')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/shopify_deployment_prod');

host('prod-ample')
    ->hostname('cap-ampleshopplugin-prod.spurtreetech.com')
    ->stage('prod-ample')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    // ->set('branch', 'master')
    ->set('branch', 'release-ample-live')
    ->set('deploy_path', '/var/www/shopify_deployment_prod_ample');

host('prod-redtag')
    ->hostname('cap-redtagshopplugin-prod.spurtreetech.com')
    ->stage('prod-redtag')
    ->user('sttdevops')
    ->port(22)
    ->identityFile('/var/lib/jenkins/sttdevops')
    ->forwardAgent(false)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->roles('app')
    ->set('branch', 'release-redtag-live')
    ->set('deploy_path', '/var/www/shopify_deployment_prod_redtag');
 


// Tasks
task('artisan:optimize', function () {});

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php7.3-fpm reload');
});

// after('deploy', 'artisan:config:clear');
// after('deploy', 'artisan:cache:clear');
// after('deploy', 'reload:php-fpm');