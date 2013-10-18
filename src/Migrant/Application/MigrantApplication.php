<?php
namespace Migrant\Application;

use Migrant\Console\ConsoleUtils;
use Migrant\Config\Config;
use Migrant\Database\Database;
use Migrant\Migration\Migration;

class MigrantApplication
{
  protected $app_name;
  protected $app_version;
  protected $util;
  private $config;

  protected $available_commands = [
    'init' => 'Prepare the configured database for use with migrant',
    'add <NAME>' => 'Create a new migration with the given name',
    'migrate' => 'Run all available migrations',
    'apply <MIGRATION>' => 'Run a specific migration',
    'reapply <MIGRATION>' => 'Re-run a specific migration that has already been run',
    'status' => 'Show which migrations need to be run',
    'help' => 'Show available commands'
  ];

  /**
   * Setup some defaults, load the config
   *
   * @param string $app_name
   * @param string $app_version
   */
  public function __construct($app_name, $app_version)
  {
    $this->app_name = $app_name;
    $this->app_version = $app_version;
    $this->util = new ConsoleUtils();

    try
    {
      $config = new Config();
    } catch (\RuntimeException $e) {
      $this->util->writeLine($e->getMessage(), 'red');
      exit;
    }

    $this->config = $config->getConfig();
  }

  /**
   * Figure out what the user is trying to do and do it
   *
   * @param array $args
   */
  public function run($args)
  {
    // If there are no params print the usage banner
    if (empty($args))
    {
      $this->util->printUsageBanner($this->app_name, $this->app_version, $this->available_commands);
      return;
    }

    try
    {
      switch(strtolower($args[0]))
      {
        case 'init':
          $this->init();
          break;
        case 'add':
          if (empty($args[1]))
            throw new \RuntimeException("No name specified for the migration!  php migrant help for available commands");
          $this->add($args[1]);
          break;
        case 'migrate':
          $this->migrate();
          break;
        case 'apply':
          if (empty($args[1]))
            throw new \RuntimeException("No migration specified!  php migrant help for available commands");
          $this->apply($args[1]);
          break;
        case 'reapply':
          if (empty($args[1]))
            throw new \RuntimeException("No migration specified!  php migrant help for available commands");
          $this->reapply($args[1]);
          break;
        case 'status':
          $this->status();
          break;
        case 'help':
          $this->util->printUsageBanner($this->app_name, $this->app_version, $this->available_commands);
          break;
        default:
          $this->util->writeLine("Unknown Command {$args[0]}", 'red');
          $this->util->writeLine();
          $this->util->printUsageBanner($this->app_name, $this->app_version, $this->available_commands);
      }
    } catch (\RuntimeException $e) {
      $this->util->writeLine($e->getMessage(), 'red');
    }
  }

  /**
   * Initialize the database for use with migrant.
   *
   */
  private function init()
  {
    // Connect to the database
    $db = new Database($this->config);

    // Check the database to see if it has already been initialized.
    $sql = 'SHOW TABLES LIKE "migrant_log"';
    $result = $db->query($sql);

    if (empty($result))
    {
      $sql = 'CREATE TABLE `migrant_log` (
              `migration_id` VARCHAR(255) NOT NULL,
              `success` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
              `executed` DATETIME NOT NULL,
              `message` TEXT NULL,
              PRIMARY KEY (`migration_id`))';
      $result = $db->execute($sql);
      if ($result != NULL)
        $this->util->writeLine('Initialization Failed! ' . $result, 'red');
      else
        $this->util->writeLine('Migrant Initialized', 'green');
    } else {
      $this->util->writeLine("Migrant has already been initialized", 'yellow');
    }
  }

  /**
   * Create a new migration
   *
   * @param string $name
   */
  private function add($name)
  {
    $now = time();
    $migration_name = 'm_' . $now . '_' . $name;

    $filename = $this->config['migration_path'] . DIRECTORY_SEPARATOR . $migration_name . '.php';

    $template = file_get_contents(__DIR__ . '/../Migration/Migration.php.tmpl');

    $template = str_replace('_migration_name_', $migration_name, $template);
    file_put_contents($filename, $template);

    $this->util->writeLine("Migration {$migration_name}.php created");
  }

  /**
   * Run the available migrations
   *
   */
  private function migrate()
  {
    $migration = new Migration($this->config);

    $migration->migrate();
  }

  /**
   * Run a specific migration
   *
   * @param string $migration
   */
  private function apply($migration)
  {
    $this->util->writeLine("Feature not yet implemented.");
  }

  /**
   * Run a specified migration again
   *
   * @param string $migration
   */
  private function reapply($migration)
  {
    $this->util->writeLine("Feature not yet implemented.");
  }

  /**
   * Output details on which migrations still need to run
   *
   */
  private function status()
  {
    $this->util->writeLine("Feature not yet implemented.");
  }
}
