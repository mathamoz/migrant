<?php
namespace Migrant\Config;

class Config
{
  protected $config;

  /**
   * Load the configuration file, or throw an error if it doesn't exist.
   *
   */
  public function __construct()
  {
    $config_file = __DIR__ . '/../../../config.json';

    if (!file_exists($config_file))
      throw new \RuntimeException("The configuration file does not exist.");

    $this->config = json_decode(file_get_contents($config_file), true);

    if (empty($this->config))
      throw new \RuntimeException("Unable to parse config file or config empty.");
  }

  /**
   * Get the configuration
   *
   * @return mixed
   */
  public function getConfig()
  {
    return $this->config;
  }
}