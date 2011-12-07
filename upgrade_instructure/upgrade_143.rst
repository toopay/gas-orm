.. Gas ORM documentation [upgrade_143]

Upgrade From 1.4.2 to 1.4.3
===========================

At version 1.4.3 [#143]_ , there is one additional file

- **application/language/spanish/gas_lang.php**

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Fix issue on validation process. 
- Fix issue on auto-migrate process at Postgre database.
- Fix issue on eager load
- Added proper comments

.. [#143] https://github.com/toopay/CI-GasORM-Library/tree/1.4.3