# Gas ORM 
Status : [![Build Status](https://secure.travis-ci.org/toopay/gas-orm.png](http://travis-ci.org/toopay/gas-orm) 

A lighweight and easy-to-use ORM for CodeIgniter. Gas was built specifically for CodeIgniter app. It uses CodeIgniter Database packages, a powerful DBAL which support numerous DB drivers. Gas ORM provide a set of methods that will map your database tables and its relationship, into accesible object.

## Requirement

* PHP >= v.5.3
* CodeIgniter v.2.x.x

## Documentation and Examples

Go to [home of Gas ORM](http://gasorm-doc.taufanaditya.com "home of Gas ORM") for full guide about convention and in-depth usage.

## Running the Test Suite

Set appropriate values on both **config/testing/database.php** and phpunit configuration you used (check **CI_VERSION**, **APPPATH** and **BASEPATH**) based by your own machine configuration. Then you could run the test, for example you use MySQL database :

	phpunit --configuration /path/to/third_party/gas/tests/travis/mysql.travis.xml --coverage-text

Gas ORM is well-tested and the latest build status could be found on the top of this document.

[![][FlattrButton]][FlattrLink] 

[FlattrLink]: https://flattr.com/submit/auto?user_id=toopay&url=https://github.com/toopay/gas-orm&title=Gas%20ORM&language=en_GB&tags=codeigniter%20orm&category=software
[FlattrButton]: http://api.flattr.com/button/button-static-50x60.png
