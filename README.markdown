# CodeIgniter Gas ORM Library

A lighweight and easy-to-use ORM for CodeIgniter

## Requirement

* PHP v.5.2.x
* CodeIgniter v.2.x.x

## About Gas

Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages of its validator class. Gas provide methods that will map your database table and its relation, into accesible object.

## Features

- Supported databases : cubrid, mssql, mysql, oci8, odbc, postgre, sqlite, sqlsrv.
- Support multiple database connection.
- Support modular models directories.
- Multiple relationship (has_one, has_many, belongs_to, has_and_belongs_to) with custom relationship setting (through, foreign_key, foreign_table, self)
- Self-referential and adjacency column/data (hierarchical data).
- Various finder method (can chained with most of CI AR) and aggregates.
- Validation and auto-mapping input collection, with minimal setup.
- Hooks points, to control over your model.
- Extensions, to share your common function/library across your model.
- Transaction, cache, and other CI AR goodness.

## Planned Features

- Auto-create and auto-synchronize tables (utilize Migration).
- Support for tree traversal.

More useful features, but keep both size and performance for a good use.

## Documentation and Examples

Go to [home of Gas ORM](http://gasorm-doc.taufanaditya.com "home of Gas ORM") for full guide about convention and in-depth usage.
