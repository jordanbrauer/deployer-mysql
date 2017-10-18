<?php

/**
 * An unofficial Deployer recipe containing a set of useful
 * tasks for interacting with MySQL locally & remotely.
 *
 * @author Jordan Brauer <jbrauer.inc@gmail.com>
 * @license MIT
 * @version 1.0.0
 *
 * @link https://github.com/deployphp/recipes
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Deployer;

# configuration
set('mysql', array(
  "host" => "localhost",
  "port" => 3306,
  "schema" => null,
  "username" => "root",
  "password" => "root",
  "dump_file" => null,
  "dump_options" => array(
    "--skip-comments",
  ),
  "restore" => function ($file) {
    return "\"source {{release_path}}/{$file};\"";
  },
));

# mysqldump
desc('Dump the database to an SQL file');
task('mysql:dump', function () {
  $config = get("mysql");
  $host = $config["host"];
  $port = $config["port"];
  $schema = $config["schema"];
  $username = $config["username"];
  $password = $config["password"];
  $file = $config["dump_file"];
  $opts = implode(" ", $config["dump_options"]);

  # check for null schema
  $schema_setting = key($schema);
  if (!$schema)
    throw new ErrorException("A schema has not been specific for use. Please set the mysql.{$schema_setting} in your deployer configuration");

  # check for null dump file
  $dump_file_setting = key($file);
  if (!$file)
    throw new ErrorException("The mysql.{$dump_file_setting} has not been set. Please set this in your deployer configuration.");

  run("mysqldump -h {$host} -P {$port} -u {$username} -p{$password} {$opts} {$schema} > {{release_path}}/{$file}");
});

# mysql-un-dump?
desc('Restore the database from an SQL file');
task('mysql:restore', function () {
  $config = get("mysql");
  $host = $config["host"];
  $port = $config["port"];
  $schema = $config["schema"];
  $username = $config["username"];
  $password = $config["password"];
  $file = $config["dump_file"];
  $restore = $config["restore"]($file);

  # check for null schema
  $schema_setting = key($schema);
  if (!$schema)
    throw new ErrorException("A schema has not been specific for use. Please set the mysql.{$schema_setting} in your deployer configuration");

  # check for null dump file
  $dump_file_setting = key($file);
  if (!$file)
    throw new ErrorException("The mysql.{$dump_file_setting} has not been set. Please set this in your deployer configuration.");

  run("mysql -h {$host} -P {$port} -u {$username} -p{$password} {$schema} -e {$restore}");
});

# scp remote database dump file to local
desc('Download the current remote SQL dump to local');
task('mysql:download', function () {
  $config = get("mysql");
  $file = $config["dump_file"];

  # check for null dump file
  $dump_file_setting = key($file);
  if (!$file)
    throw new ErrorException("The mysql.{$dump_file_setting} has not been set. Please set this in your deployer configuration.");

  download('{$file}', '{{release_path}}/{$file}');
});

# scp local database dump file to remote
desc('Upload the current local SQL dump to remote');
task('mysql:upload', function () {
  $config = get("mysql");
  $file = $config["dump_file"];

  # check for null dump file
  $dump_file_setting = key($file);
  if (!$file)
    throw new ErrorException("The mysql.{$dump_file_setting} has not been set. Please set this in your deployer configuration.");

  upload('{$file}', '{{release_path}}/{$file}');
});

# fetch a fresh copy of the database
desc('Fetch a fresh copy of the remote SQL dump');
task('mysql:pull', [
  'mysql:dump',
  'mysql:download',
]);
