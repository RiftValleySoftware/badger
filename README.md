\page BADGER BADGER

![BADGER](images/BADGER.png)

BADGER
======

Part of the BAOBAB Server, which is part of the Rift Valley Platform
--------------------------------------------------------------------
![BAOBAB Server and The Rift Valley Platform](images/BothLogos.png)

INTRODUCTION
============
This is the baseline database system for the secure database toolbox.

![BADGER Diagram](images/BADGERLayers.png)

It's a low-level database storage system that implements a generic database and a separate security database.

BADGER will work equally well for both [MySQL](https://www.mysql.com) and [PostgreSQL](https://www.postgresql.org) databases.

BADGER has two completely separate databases: One is the main "Data Bucket" database, and the other is a security database. They can be separate hosts and even database types ([MySQL](https://www.mysql.com) or [PostgreSQL](https://www.postgresql.org)).

BADGER has an ultra-simple schema. Each database has only one table.

Security is via a simple token arrangement.

Each database table row (either database) has a read token and a write token. It has only one of each. If the read or write operation is attempted by a user without the token, then the record is automatically excluded from the results at the SQL level. Tokens are IDs for security database nodes. A login node (security database) has its own ID as a token, and can also have a list of IDs that are available to it as well.

BADGER is extremely basic. No relating or data processing is done by BADGER, with one exception: it does have longitude and latitude (in degrees) built into its table schema, for the simple reason that being able to access these at the SQL level greatly improves performance. We  have a built-in [Haversine](https://en.wikipedia.org/wiki/Haversine_formula) search in SQL to act as a "rapid triage" for locations, which are then filtered using the more accurate (and computationally-intense) [Vincenty's Formulae](https://en.wikipedia.org/wiki/Vincenty%27s_formulae). This means that geographic location/radius searches are extremely accurate, and very fast.

DESCRIPTION
===========

PDO
---

At the lowest level, the system uses [PHP PDO](https://php.net/pdo) to access the databases through [PDO Prepared Statements](https://secure.php.net/manual/en/pdo.prepared-statements.php).

This enhances security by ensuring that all database access is "scrubbed" by [PDO](https://php.net/pdo), so the risk of [SQL injection attacks](https://www.w3schools.com/sql/sql_injection.asp) is greatly reduced.

[PDO](https://php.net/pdo) also helps the system to be "database agnostic," as [PDO](https://php.net/pdo) has equal support for both [MySQL](https://www.mysql.com) and [PostgreSQL](https://www.postgresql.org). It is designed to scale for large datasets.

TWO INDEPENDENT DATABASES
-------------------------

The fundamental database/storage structure is that there are two separate databases (not just different tables on a single database), with one containing data, and the other containing logins and security tokens.

Each database is managed by its own [PDO](https://php.net/pdo) instance, so they don't need to be on the same server, or even be the same technology.

MYSQL AND POSTGRESQL
--------------------

The Rift Valley Platform has been designed to allow seamless implementation with either [MySQL](https://www.mysql.com) or [PostgreSQL](https://www.postgresql.org). It is as simple as switching the driver type in the config file. There is no penalty for using either one.

The databases can also be mixed. You can have, for example, a [MySQL](https://www.mysql.com) database, hosted in a hardened facility, as your Security database, and a local [PostgreSQL](https://www.postgresql.org) database as your main data database.

SECURITY
========

SECURITY TOKENS
---------------

Access to read and write data entities is determined by "security tokens." Security tokens are simple integers, each representing an ID in the security database. Database table rows are assigned a read token and a write token.

Logins in the security database are assigned a series of integer tokens in a [CSV](https://en.wikipedia.org/wiki/Comma-separated_values) list, in addition to their own ID. If the login item has the token for reading or writing assigned to a particular row, then that login has permission to read (and maybe write) that row.

Badger is designed to mask rows that don't meet the login security token list at the database query level, so they never even make it into the system.

Tokens are checked after reloading the logged in user, making it quite difficult for a logged-in user to escalate their permissions.

The security database has two kinds of rows: logins and security token IDs (Whose entire reason for existence is to hold a security token). Each login is also a security token ID, but has a hashed password and login ID associated.

BADGER has the basic login class (`CO_Security_Login`), and the token placeholder class (`CO_Security_ID`). These are both security database classes (not applied to the data database).

\ref CHAMELEON will extend the `CO_Security_Login` class to be the `CO_Login_Manager` class, but that's a topic for a different day...

THERE IS NO GOD BUT GOD
-----------------------

Only one of the logins can be a "God" login; a login that has full permissions to everything. There are no "security levels." All security is done through tokens. The "God" ID is set as an integer ID in the config file, and the password is also stored (as cleartext) in the config file.

STRUCTURE
=========

CONFIG FILE
-----------

The config file can (and should) be kept out of the HTTP path, making it harder for outside entities to access.

The config file consists of a static class, with "hardcoded" constants. The class exists mainly as a namespace.

KEEPING IT SIMPLE
-----------------

The database schema is incredibly simple. There are no relations at the database level.

Every row (in either database) has an "id" column, containing a BIGINT. This ID is completely unique (within the database) and auto-incremented. In the data database, this can be used in the "owner" column, and in the security database, this column is a security token. Regardless of the instance associated with a security database row, the ID is a valid security token.

Each database has a row with ID of 1. This is a simple "template" row, used for instantiating new rows. IDs start at 2.

Each database has but one single table, and the data format (the columns) are the same for all rows of that table. Differentiation of instances spawned from the database is done through the "access_class" column, which contains the name of a PHP class that can handle that row. There is also an "access_class_context" column, which contains serialized data, storing a persistent state for the class instance.

Both the "data" database and the "security" database tables have the same basic root structure, and are handled by a common abstract base class.

Hierarchy and organization are meant to be applied outside the database, using classes and instances. The database is really just supposed to be a "locked cabinet."

The design of the database is NOT the most efficient design in the world, but it will work extremely well for smaller datasets, is flexible, robust and secure. The Rift Valley Platform has been designed as a general-purpose workhorse application; not a big data storage system. It tends to be more heavily weigthed towards security, rather than efficiency.

It's designed to be replaced. Keeping the storage metaphor to a fairly linear, non-related structure opens up tremendous opportunities for swapping out the lowest layer. The "layer cake" approach to the RIFT Valley Platform structure also allows you to replace very fundamental aspects of the system.

OBJECT PERSISTENCE
------------------

BADGER is actually an "ORM" engine, as opposed to a traditional database. Each row of either database has an `"access_class"` column. This is the classname of the instance that has its data stored in that row, and reading in the row will instantiate that class.

Each row also has an `"access_class_context"` column, which contains a serialized associative array, called `"context"` inside the instance. Whatever is in that array will be stored and restored.

LONGITUDE AND LATITUDE
----------------------

The one built-in specialization is the inclusion of `"longitude"` and `"latitude"` (in degrees) columns in the "data" database table. This is because it is possible to do highly efficient, fast [Haversine](https://en.wikipedia.org/wiki/Haversine_formula) lookups in SQL. We have built these into the system.

Once a Haversine search has been made, the resulting data set is then subjected to the more accurate [Vincenty's Formula](https://en.wikipedia.org/wiki/Vincenty%27s_formulae) in order to "fine tune" the result.

The result of this, is that Badger is an ideal vehicle to keep a database of locations for fast retrieval, based on longitude and latitude, and a radius.

TAGS
----

Each "data" database row has ten `"tags"`. These are 255-character `"VARCHAR"` fields that can be used for many things. They are indexed for fast search (thus, are limited in length).

PAYLOAD
-------

The "data" database schema also specifies a `"TEXT"` ([PostgreSQL](https://www.postgresql.org)) or `"LONGBLOB"` ([MySQL](https://www.mysql.com)) column, called `"payload"`. This is used to store larger data with a data item. It is not indexed, and can store binary (and encrypted) data, but the data is stored internally as [Base64](https://en.wikipedia.org/wiki/Base64).

IMPLEMENTATION
==============

You implement BADGER by setting up a pair of databases, using the `CO_Config` class, and then instantiate `CO_Access`. This will be used as the "control panel" of BADGER.

EXTENDING AND SPECIALIZING BADGER
=================================

BADGER is a baseline system. It provides a generic interface to a simple database, and is not designed to be used "as is." It should be extended via subclasses of the row classes and the access class.

In order to extend the row classes, you should create a directory (path and name determined by the `CO_Config` class). Put your classes that extend the class in `"db/a_co_db_table_base.class.php"` there (actually, you should be extending subclasses of this base class). It does not need to be in the HTTP path.

The files containing classes should be named after the class, all lower case, with `'.class.php'` appended.

LICENSE
=======

![The Great Rift Valley Software Company LLC](images/viper.png)
© Copyright 2018, The Great Rift Valley Software Company

LICENSE:

MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

The Great Rift Valley Software Company: https://riftvalleysoftware.com
The Great Rift Valley Software Company: https://riftvalleysoftware.com

