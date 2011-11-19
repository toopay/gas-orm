.. Gas ORM documentation [upgrade_120]

Upgrade From 1.1.2 to 1.2.0
===========================

At version 1.2.0 [#120]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Custom relationship setting (through, foreign_key, foreign_table).
- Hooks points a.k.a Callbacks.

.. [#120] https://github.com/toopay/CI-GasORM-Library/tree/1.2.0