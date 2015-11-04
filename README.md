
# Jaya-CMS

A file-based, git-oriented, CMS written in PHP.

[![Build Status](https://travis-ci.org/Etskh/Jaya-CMS.svg?branch=master)](https://travis-ci.org/Etskh/Jaya-CMS)

It was started for my own use as a personal blog, portfolio site - because it's strange to advertise your LAMP skills with a Wordpress blog. Since then, it's evolved into a general CMS with much inspiration from ExpressJS and Django, but as a low-latency PHP project. Easier to set up, and much more support for speed increases.

# Installing
## Clone this Project

Just clone it into the folder

## Import the Submodules

Run `git submodule foreach git pull origin master` because it's good for the soul. If that doesn't work, no worries. `cd` into `modules/extern` and run this command for each folder: `git submodule update --init {folder-name}`


# Generating the docs

Code should document itself, right? This codebase is meant to run with `apigen` to generate its documentation. So to run the documentation generator, just throw down the command: `apigen --exclude="*extern*" --source="./modules/" --destination="./docs" generate`


# Testing

I use PHPUnit to do testing, as it is the default for TravisCI environment. Jaya-CMS is also tested on PHP 5.3, 5.4, 5.5 and 5.6. Testing can be manually run with `phpunit --bootstrap ./modules/core/Bootstrap.php ./tests`

# Contact

 * Etskh - primary developer.
