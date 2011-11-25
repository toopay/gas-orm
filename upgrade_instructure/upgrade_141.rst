.. Gas ORM documentation [upgrade_141]

Upgrade From 1.4.0 to 1.4.1
===========================

At version 1.4.1 [#141]_ , there are several additional file

- **application/libraries/Gas_extension_html.php**
- **application/libraries/Gas_extension_jquery.php**

Step 1 : Update your Gas ORM files
++++++++++++++++++++++++++++++++++

Replace old files with the new one. The main change is on **application/libraries/Gas.php** :

- Support unique field.
- Support auto-created date/timestamp (for INSERT or UPDATE operation).

And there are two additional extension, html and jquery to help us :

- generate HTML form.
- generate HTML table.
- handling and generate datatable (jQuery) response.

.. [#141] https://github.com/toopay/CI-GasORM-Library/tree/1.4.1