.. Gas ORM documentation [upgrade_131]

Upgrade From 1.3.0 to 1.3.1
===========================

At version 1.3.1 [#131]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Adding '.svn' to exception folder in scanning models method.

.. [#131] https://github.com/toopay/CI-GasORM-Library/tree/1.3.1