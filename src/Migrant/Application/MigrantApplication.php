<?php
namespace Migrant\Application;

use Migrant\Console\ConsoleUtils;
use Migrant\Config\Config;

class MigrantApplication
{
  protected $app_name;
  protected $app_version;
  protected $util;
  private $config;

  protected $available_commands = [
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

  }

  /**
   * Run a specific migration
   *
   * @param string $migration
   */
  private function apply($migration)
  {

  }

  /**
   * Run a specified migration again
   *
   * @param string $migration
   */
  private function reapply($migration)
  {

  }

  /**
   * Output details on which migrations still need to run
   *
   */
  private function status()
  {

  }
}
