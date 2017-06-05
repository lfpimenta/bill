# Challenge

I accepted the challenge [call billing](problems/b3_call_billing.md).

The objective is to create a console application to charge incoming calls, and list all charges per account.

## Technologies used

To create the application I used:
 * PHP
 * Mysql
 * PhpUnit
 * [Composer](https://getcomposer.org/)
 * External packages:
   * [Symfony Console](https://packagist.org/packages/symfony/console)
   * [csv/league](https://packagist.org/packages/league/csv)

## Project Setup

###Prerequisites

To setup the project it is necessary to have installed on your machine:

 * PHP
 * Composer
 * MySql
 * PhpUnit

### Get external dependencies

First of all we need to get the necessary external packages with the help of composer
```bash
$ composer install
```
### Initial setup

Next we need to create the database to be the application storage

```mysql
$ mysql -u<user> [-p<password>]

Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 308
Server version: 10.1.9-MariaDB Source distribution

Copyright (c) 2000, 2015, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]> create database talkdesk;
Query OK, 1 row affected (0,00 sec)

```

After creating database we need to restore database structure usefull for the project

```bash
$ mysql -u root talkdesk < assets/DB/initial-mysql.sql
```

In order to billing is possible we should have at least one account with credit, for that we can insert in db account like this:

```bash
insert into account (name, credit) values ('Luis', 300);
```

### Configuration

There is a file named *config.ini* that has the settings to:
 * access database
    * host
    * user
    * database name
    * password
 * the files paths used to get the cost values
    * toll free/not free
    * twillio numbers costs
    * margin


## How to Use

Command is named *call_billing*, when invoked without arguments it will show the available commands
 and is invoked like so:

```bash
php call_billing.php
```

For help with a particular command you can write

```bash
php call_billing.php help <command>
```

The implemented commands are:

* **chargeCall** - Charges a call and remove credits from a given account
* **listInboundCalls** - Lists the charges for the given account

####Examples

#####Add an inbound call
 * lasts for **100** seconds,
 * for **Luis** account,
 * with talkdesknumber **12345545678**,
 * from client number **22422444446**
 * the call was taken to a forwarded phone with number **180022344**

```bash
php call_billing.php chargeCall 100 "Luis" 12345545678 22422444446 180022344
```

#####List charges for user "Luis"
```bash
php call_billing.php listCalls "KLuis"
```

## Assets used for costs calculation

* File [tdMargin.csv](assets/Csv/tdMargin.csv):
  * Has the rules for margin calculation
  * Define the ranges of minutes used and the the correspondant  margin
  * These values are used with the current month minutes used by the particular account
  * If user has a lot of minutes used then our margin decreases...
* File [tollFreeNumbers.csv](assets/Csv/tollFreeNumbers.csv):
  * Has the begin of US and UK Toll free numbers, as it's cost
  * Fallback is 1cent
* File [Twilio - Voice Prices.csv](/problems/assets/call%20billing/Twilio%20-%20Voice%20Prices.csv)
  * Has the prices per location
  * This is only considered if call is forwarded to a regular phone

## Running the tests

To run int tests that assert that the cost asserts are ok, simply do this:

```bash
$ cd tests
$ phpunit
```
