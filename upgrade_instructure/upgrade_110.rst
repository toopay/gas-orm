.. Gas ORM documentation [upgrade_110]

Upgrade From 1.0.3 to 1.1.0
===========================

At version 1.1.0 [#110]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Support (deep and) multiple relationships.
- Support multiple database connection.
- Support transaction.
- Remove has_result() method, which become non-uniformal method for finder.

.. [#110] https://github.com/toopay/CI-GasORM-Library/tree/1.1.0