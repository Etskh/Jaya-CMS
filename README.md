# Jaya-CMS
A file-based CMS written in PHP, with support for Markdown

# Installing

## Clone this Project

Just clone it into the folder

## Import the Submodules

Run `git submodule foreach git pull origin master` because it's good for the soul.

If that doesn't work, no worries. `cd` into `modules/extern` and run this command for each folder:

`git submodule update --init {folder-name}`

## Run the tests

I use PHPUnit to do testing, as it is the default for TravisCI environment. Jaya-CMS is also tested on PHP 4.8, 5.3, 5.4, 5.5 and 5.6.

Testing can be manually run with `phpunit ./tests`
