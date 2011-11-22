.. Gas ORM documentation [reserved]

Reserved Names
==============

In order to fully working, Gas ORM uses a series of functions and names in its operation. Because of this, some names cannot be used by you, in your application to avoid overide or/and any other collision. Following is a list of reserved names that cannot be used.

Class Names
+++++++++++

If you use Gas ORM in your application, avoid to have these list in your classes name.

- Gas_core
- Gas_bureau
- Gas_janitor
- Gas_extension
- Gas

And optionaly, there is several generic extensions available as default. If you doesnt use them, you are not need to concern bellow list.

- Gas_extension_dummy

Function Names
++++++++++++++

All of your Gas model, is extending **Gas** class and therefore, **Gas_core** class. Avoid to have this methods/functions name in your Gas model.

- version()
- factory()
- connect()
- recruit_bureau()
- is_initialize()
- is_migrated()
- load_model()
- load_extension()
- reports()
- flush_cache()
- field()
- check_migration()
- db()
- list_all_models()
- list_models()
- add_ar_record()
- get_type()
- get_with()
- get_with_models()
- get_config()
- get_raw_record()
- get_ar_record()
- get_extensions()
- set_type()
- set_fields()
- set_record()
- set_reflection_record()
- set_child()
- set_ar_record()
- with()
- produce()
- all()
- first()
- last()
- min()
- max()
- avg()
- sum()
- last_id()
- list_fields()
- last_sql()
- all_sql()
- find()
- find_where_in()
- find_where()
- save()
- delete()
- tell()
- set_message()
- set_error()
- errors()
- auto_check()
- char_check()
- to_array()
- to_json()
- model()
- identifier()
- validate_table()
- validate_join()
- fill()
- filled_fields()
- _scan_models()
- _scan_extensions()
- _scan_files()

Properties Names
++++++++++++++++

All of your Gas model, is extending **Gas** class and therefore, **Gas_core** class. Avoid to have this properties name in your Gas model, unless you really know what you are doing (like dynamically overide **table** name or something that you really know its effects).

- $table
- $primary_key
- $relations
- $empty
- $errors
- $locked
- $single
- $extensions
- $loaded_models
- $childs
- $childs_resource
- $init
- $bureau
- $ar_recorder
- $post
- $join
- $with
- $with_models
- $config
- $transaction_pointer
- $selector
- $condition
- $executor
- $transaction_status
- $transaction_executor
- $_models
- $_models_fields
- $_extensions
- $_rules
- $_error_callbacks
- $_errors_validation
- $_fields
- $_set_fields
- $_get_fields
- $_get_child_fields
- $_get_child_nodes
- $_get_reflection_fields

Constant Names
++++++++++++++

- GAS_VERSION