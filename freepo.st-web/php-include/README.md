freepost
=======

This is the code powering [freepost](http://freepo.st).

Installation
=========

You can follow these steps if you wish to run a local copy of freepost

 1. `cd` into your `public_html` folder
 2. `$ git clone git://git.savannah.nongnu.org/freepost.git`
 3. configure your database following [these instructions](http://symfony.com/doc/current/book/doctrine.html#configuring-the-database). In short,
  1. `$ cd freepo.st-web/php-include/`
  2. edit the file `app/config/parameters.example.yml` and rename it to `parameters.yml`
  3. manually create freepost database, for example using phpMyAdmin. Make sure that it's UTF8
  4. `$ php app/console doctrine:schema:update --force`

freepost should now be up and running at `http://localhost/freepost/freepo.st-web/htdocs`

License
======

freepost is [free software](https://www.gnu.org/philosophy/free-sw.html) licensed as GNU Affero General Public License, either version 3 or (at your option) any later version.
freepost source files and license are inside the `./freepo.st-web/php-include/src/` directory. Everything else is part of the Symfony2 framework and other dependencies.
