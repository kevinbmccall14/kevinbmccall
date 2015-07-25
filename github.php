<?php

// Set Variables
$LOCAL_ROOT         = "~/projects/kevinbmccall";
$LOCAL_REPO_NAME    = "kevinbmccall";
$LOCAL_REPO         = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
$REMOTE_REPO        = "git@github.com:kmccallsdsu/kevinbmccall.git";
$BRANCH             = "master";

$payload_github = file_get_contents('php://input');
$data = json_decode($payload_github);

if ( $data->ref === 'refs/heads/master' ) {

  // Only respond to POST requests from Github
  echo "Payload received from GitHub".PHP_EOL;

  if( file_exists($LOCAL_REPO) ) 
  {

    $whoami = shell_exec("whoami");
    echo "whoami: $whoami".PHP_EOL;

    // If there is already a repo, just run a git pull to grab the latest changes       
    $git_pull = shell_exec("git pull 2>&1");
    echo "Git Pull: $git_pull".PHP_EOL;

    // $composer_install = shell_exec("cd $LOCAL_REPO && composer install 2>&1");
    // echo "Composer Install: $composer_install".PHP_EOL;

    $artisan_dump = shell_exec("cd $LOCAL_REPO && php artisan dump-autoload 2>&1");
    echo "PHP Artisan Dump-Autoload: $artisan_dump".PHP_EOL;

    $artisan_migrate = shell_exec("cd $LOCAL_REPO && php artisan migrate --env=production 2>&1");
    echo "PHP Artisan Migrate: $artisan_migrate".PHP_EOL;

    die("The End! " . mktime());    
  } 
  else 
  {

    // If the repo does not exist, then clone it into the parent directory

    shell_exec("cd {$LOCAL_ROOT} && git clone {$REMOTE_REPO} {$LOCAL_REPO_NAME}");
    echo "git clone: repo cloned successfully!".PHP_EOL;

    shell_exec("cd {$LOCAL_REPO} && composer install");
    echo "Executed: composer install".PHP_EOL;

    die("The End! " . mktime());
  }
} 

else {
    echo "Payload is not from GitHub. Nothing to see here!".PHP_EOL;
}