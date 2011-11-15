.. Gas ORM documentation [what_is_gas_orm]

What is Gas ORM ?
=================

A **lighweight** [#light]_ and **easy-to-use** [#easy]_ ORM for CodeIgniter. Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages from CI validator class. Gas ORM provide a set of methods that will map your database tables and its relationship, into accesible object.

Gas ORM is Object Relational Mapping...
+++++++++++++++++++++++++++++++++++++++

Object-relational mapping (ORM, O/RM, and O/R mapping) in computer software is a programming technique for converting data between incompatible type systems in object-oriented programming languages. This creates, in effect, a **"virtual object database"** that can be used from within the programming language. [#orm_wikipedia]_ 


Gas ORM is follow Active Record pattern...
++++++++++++++++++++++++++++++++++++++++++

Active record is an approach to accessing data in a database. A database table or view is wrapped into a class. Thus, **an object instance is tied to a single row in the table**. After creation of an object, a new row is added to the table upon save. Any object loaded gets its information from the database. When an object is updated the corresponding row in the table is also updated. The wrapper class implements accessor methods or properties for each column in the table or view. [#ar_wikipedia]_ 

Gas ORM is for CodeIgniter...
+++++++++++++++++++++++++++++
CodeIgniter is most often noted for its speed when compared to other PHP frameworks. In a critical take on PHP frameworks in general, PHP creator Rasmus Lerdorf spoke at frOSCon in August 2008, noting that he liked CodeIgniter "because it is faster, lighter and the least like a framework." Gas ORM is perfect choice, if you want to have a **stable** [#save]_, **light** and **fast** ORM for your CodeIgniter application. [#benchmark]_  

Gas ORM is Open Source...
+++++++++++++++++++++++++
Gas ORM is licensed under BSD. [#bsd]_  


| Copyright 2011 Taufan Aditya a.k.a toopay. All rights reserved.
|
| Redistribution and use in source and binary forms, with or without modification, are
| permitted provided that the following conditions are met:
| 
| 1. Redistributions of source code must retain the above copyright notice, this list of
|    conditions and the following disclaimer.
| 
| 2. Redistributions in binary form must reproduce the above copyright notice, this list
|    of conditions and the following disclaimer in the documentation and/or other materials
|    provided with the distribution.
| 
| THIS SOFTWARE IS PROVIDED BY Taufan Aditya a.k.a toopay ''AS IS'' AND ANY EXPRESS OR IMPLIED
| WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
| FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Taufan Aditya a.k.a toopay OR
| CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
| CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
| SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
| ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
| NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
| ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
| 
| The views and conclusions contained in the software and documentation are those of the
| authors and should not be interpreted as representing official policies, either expressed
| or implied, of Taufan Aditya a.k.a toopay.



.. [#light] The main library's file size is just 57 Kb.
.. [#easy] If you follow the convention, the setup is minimal.
.. [#orm_wikipedia] http://en.wikipedia.org/wiki/Object-relational_mapping.
.. [#ar_wikipedia] http://en.wikipedia.org/wiki/Active_record.
.. [#save] There is gasunittest.php contain unit testing, to ensure all method working properly as it should.
.. [#benchmark] https://github.com/toopay/CI-GasORM-Library/downloads.
.. [#bsd] http://en.wikipedia.org/wiki/BSD_licenses

