.. Gas ORM documentation [upgrade_133]

Upgrade From 1.3.2 to 1.3.3
===========================

At version 1.3.3 [#133]_ , there is no file added, it has same structure as previous version.

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Fully support latest stable CI 2.1.0.
- Better (raw) performance. Change several fraction within compile method, but recent legacy API is not (and will never be) changed.
- Fix some validation issues, it is a way more stable than previous v.1.3.x.

.. [#133] https://github.com/toopay/CI-GasORM-Library/tree/1.3.3