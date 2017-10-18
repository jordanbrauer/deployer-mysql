# deployer-mysql

An unofficial [Deployer](https://github.com/deployphp/deployer) recipe containing a set of useful tasks for interacting with MySQL locally &amp; remotely.

## Usage

### Installation

Install via Composer as a dev' dependency to your project.

```shell
$ composer require --dev jordanbrauer/deployer-mysql
```

### Basic Setup

Add the following to your deployer config;

```php
require_once "mysql.php";

set("mysql", array(
  "host" => "localhost",
  "port" => 3306,
  "schema" => "your_database_name",
  "username" => "root",
  "password" => "root",
  "dump_file" => "path/to/your/dump/file.sql",
));
```

### Additional Setup

If you want to add options (flags) to your `mysqldump` command task, you can do so by adding the "`dump_options`" key to the configuration array, like so;

```php
set("mysql", array(
  // ...
  "dump_options" => array(
    "--skip-comments",
  ),
));
```

Each option you want to add must be a new entry in the array.

_**Note:** the_ `--skip-comments` _option is the only default option set. So, if you don't have any other options for your setup, you can omit this configuration key entirely._
