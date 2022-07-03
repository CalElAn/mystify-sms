#!/bin/sh
set -e

##first git add, commit and run . local_deploy.sh 

#vendor/bin/phpunit
vendor/bin/sail php artisan test
 
(git push) || true
 
git checkout production
git merge main
 
git push origin production
 
git checkout main