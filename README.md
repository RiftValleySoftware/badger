![Honey Badger Don't Care](icon.png)

INTRODUCTION
============

***[Hardened Baseline Database Component](https://en.wikipedia.org/wiki/The_Crazy_Nastyass_Honey_Badger#Honey_Badger_Don't_Care)***

This is the baseline database system for the secure database toolbox.
It's a low-level database storage system that implements a generic KVP database and a separate security database.

LICENSE
=======
![Little Green Viper Software Development LLC](spec/viper.png)
© Copyright 2018, [Little Green Viper Software Development LLC](https://littlegreenviper.com).
This code is ENTIRELY proprietary and not to be reused, unless under specific, written license from [Little Green Viper Software Development LLC](https://littlegreenviper.com).

DESCRIPTION
===========

PDO
---

At the lowest level, the system uses [PHP PDO](https://php.net/pdo) to access the data base through [PDO Prepared Statements](https://secure.php.net/manual/en/pdo.prepared-statements.php).
This enhances security by ensuring that all database access is "scrubbed" by PDO, so the risk of [SQL injection attacks](https://www.w3schools.com/sql/sql_injection.asp) is greatly reduced.
PDO also helps the system to be "database agnostic," as PDO has support for multiple SQL-based databases.