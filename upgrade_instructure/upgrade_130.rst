.. Gas ORM documentation [upgrade_130]

Upgrade From 1.2.1 to 1.3.0
===========================

At version 1.3.0 [#130]_ , there is one file added :

- **application/libraries/Gas_extension_dummy.php**

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Support extensions.

If you wish to use extension(s) or create your own, you should replace all old language files and config file with the new one.

.. [#130] https://github.com/toopay/CI-GasORM-Library/tree/1.3.0