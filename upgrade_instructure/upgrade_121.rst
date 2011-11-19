.. Gas ORM documentation [upgrade_121]

Upgrade From 1.2.0 to 1.2.1
===========================

At version 1.2.1 [#121]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Self-referential and adjacency column(hierarchical data).
- Adding version constant.
- Small refactoring.

.. [#121] https://github.com/toopay/CI-GasORM-Library/tree/1.2.1