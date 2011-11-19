.. Gas ORM documentation [upgrade_103]

Upgrade From 1.0.2 to 1.0.3
===========================

At version 1.0.2 [#102]_ , there are three files :

- **application/config/gas.php**
- **application/controllers/gasunittest.php**
- **application/libraries/Gas.php**

Start from version 1.0.3 [#103]_ and up to the latest version, there is several additional file

- **application/language/english/gas_lang.php**
- **application/language/indonesia/gas_lang.php**
- **application/language/italian/gas_lang.php**

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Break Gas into 3 different class : **Gas_core** , **Gas_bureau** and **Gas_janitor**, with **Gas** inherit from Gas_core, within one file **Gas.php**.

This changes is actually related with CI changes.

Step 2 : Include all languange files
++++++++++++++++++++++++++++++++++++

Those language files contain line which used by Gas, most of it is to yield some error/warning.

.. [#102] https://github.com/toopay/CI-GasORM-Library/tree/1.0.2
.. [#103] https://github.com/toopay/CI-GasORM-Library/tree/1.0.3