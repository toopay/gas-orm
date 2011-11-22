.. Gas ORM documentation [upgrade_140]

Upgrade From 1.3.3 to 1.4.0
===========================

At version 1.4.0 [#140]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Queries optimization. Better (raw) performance of speed, memory usage and request concurency.
- Per-request caching.
- Auto-generate models from database schema and vice versa. Both process will also generate all models sibling's file : migration files respectively

.. [#140] https://github.com/toopay/CI-GasORM-Library/tree/1.4.0