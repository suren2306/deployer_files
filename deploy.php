<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('laravelApp', 'new_app');

// Project repository
set('repository', 'https://github.com/suren2306/laravel_deployer.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('staging.lnmp')
//Host details configured in .ssh/config file
    ->set('deploy_path', '/mnt/data/sites/laravel_deployer/');
    
// Tasks
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:optimize',
    'artisan:migrate',
    'deploy:symlink',
    'keygen',
    'deploy:unlock',
    'cleanup',
    'success'
]);

task('build', function () {
    run('cd {{release_path}} && build');
});

task ('keygen',function(){
    run('php {{deploy_path}}/current/artisan key:generate && php {{deploy_path}}/current/artisan config:clear');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

