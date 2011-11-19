.. Gas ORM documentation [upgrade_112]

Upgrade From 1.1.1 to 1.1.2
===========================

At version 1.1.2 [#112]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Support custom key name and custom table name, in relationship properties.
- Fix minor bug and typo.

.. [#112] https://github.com/toopay/CI-GasORM-Library/tree/1.1.2