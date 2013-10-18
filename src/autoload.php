<?php

require __DIR__ . '/Migrant/Config/Config.php';

function autoload($className)
{
  $config = new Migrant\Config\Config();
  $config = $config->getConfig();

  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strripos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }

  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  $migrationFilename = $config['migration_path'] . DIRECTORY_SEPARATOR . $className . '.php';

  if (file_exists($migrationFilename))
  {
    require $migrationFilename;
    return;
  }

  require $fileName;
}

spl_autoload_register('autoload');