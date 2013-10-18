<?php
namespace Migrant\Migration;

use Migrant\Console\ConsoleUtils;
use Migrant\Database\Database;

class Migration
{
  protected $db;
  protected $config;
  protected $util;

  /**
   * Setup a connection to the database
   *
   * @param $config
   */
  public function __construct($config)
  {
    $this->config = $config;
    $this->db = new Database($config);
    $this->util = new ConsoleUtils();
  }

  /**
   * Run all migrations that need to be run
   *
   */
  public function migrate()
  {
    //TODO: Allow actions based on success of previous runs
    //TODO: Allow notification of failure via email?
    // An array of migrations that need to be run
    $to_run = [];
    // Array of migrations that have already run
    $have_run = [];

    // Get a list of migrations that have already been run.
    $sql = 'SELECT migration_id, success FROM migrant_log';
    $results = $this->db->query($sql);

    foreach ($results as $result)
      $have_run[] = $result['migration_id'];

    // Get a list of available migrations
    $migration_dir = $this->config['migration_path'];

    $migrations = scandir($migration_dir);

    $ignore_files = ['.', '..', '.gitignore'];

    foreach ($migrations as $migration)
    {
      if (!in_array($migration, $ignore_files) && !in_array(explode('.', $migration)[0], $have_run))
        $to_run[] = $migration;
    }

    // Make sure we run the migrations in the correct order
    $to_run = $this->sortMigrations($to_run);

    foreach ($to_run as $runnable)
    {
      //require (__DIR__ . '/../../../migrations/' . $runnable);
      $classname = __NAMESPACE__ . '\\' . explode('.', $runnable)[0];
      $migration = new $classname($this->config);

      $this->util->writeLine('Running Migration: ' . explode('.', $runnable)[0], 'green');
      $result = $migration->up();

      if ($result['success'])
      {
        $sql = 'INSERT INTO migrant_log VALUES(?,?,?,?)';
        $this->db->insert($sql, [explode('.', $runnable)[0], 1, date("c"), '']);
      } else {
        $sql = 'INSERT INTO migrant_log VALUES(?,?,?,?)';
        $this->db->insert($sql, [explode('.', $runnable)[0], 0, date("c"), $result['message']]);
      }
    }

    if (empty($to_run))
      $this->util->writeLine('All migrations have been run', 'green');
  }

  /**
   * Sort the migrations to ensure that they are run in the correct order
   *
   * @param $migrations
   * @return array
   */
  private function sortMigrations($migrations)
  {
    uasort($migrations, function($a, $b) {
      $a = explode('_', $a)[1];
      $b = explode('_', $b)[1];

      if ($a == $b)
        return 0;
      return ($a < $b) ? -1 : 1;
    });

    return $migrations;
  }
}