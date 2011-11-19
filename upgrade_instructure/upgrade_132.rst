.. Gas ORM documentation [upgrade_132]

Upgrade From 1.3.1 to 1.3.2
===========================

At version 1.3.2 [#132]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Fix some fraction of validation mechanism issue(s), validatot even can handle PUT data. Also handling validation error message could be more freely by accesing **errors** properties of gas instance.
- Adding **[min-length,max-length]** option directly in field method.
- Fully support :doc:`modular models directories <../configuration>` even if modules if outside the application path. 

.. [#132] https://github.com/toopay/CI-GasORM-Library/tree/1.3.2