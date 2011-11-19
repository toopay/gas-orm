.. Gas ORM documentation [upgrade_102]

Upgrade From 1.0.1 to 1.0.2
===========================

At version 1.0.1 [#101]_ , there are two files :

- **application/config/gas.php**
- **application/libraries/Gas.php**

Start from version 1.0.2 [#102]_ and up to the latest version, there is one additional file

- **application/controllers/gasunittest.php**

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Validating table before running count.
- Fixed some issues related with CI Form Validation.

Step 2 : Include gasunittest.php
++++++++++++++++++++++++++++++++

This is optional step. Additional file, which is **application/controllers/gasunittest.php** intend to use to perform unit testing via browser, and as quick lookup since its contain both basic convention and basic documentation of how to use Gas ORM.


.. [#101] https://github.com/toopay/CI-GasORM-Library/tree/1.0.1
.. [#102] https://github.com/toopay/CI-GasORM-Library/tree/1.0.2