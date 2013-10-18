<?php
namespace Migrant\Console;

class ConsoleUtils
{
  protected $foreground = [];
  protected $background = [];

  public function __construct()
  {
    // Setup console colors
    $this->foreground['black'] = '0;30';
    $this->foreground['dark_gray'] = '1;30';
    $this->foreground['blue'] = '0;34';
    $this->foreground['light_blue'] = '1;34';
    $this->foreground['green'] = '0;32';
    $this->foreground['light_green'] = '1;32';
    $this->foreground['cyan'] = '0;36';
    $this->foreground['light_cyan'] = '1;36';
    $this->foreground['red'] = '0;31';
    $this->foreground['light_red'] = '1;31';
    $this->foreground['purple'] = '0;35';
    $this->foreground['light_purple'] = '1;35';
    $this->foreground['brown'] = '0;33';
    $this->foreground['yellow'] = '1;33';
    $this->foreground['light_gray'] = '0;37';
    $this->foreground['white'] = '1;37';

    $this->background['black'] = '40';
    $this->background['red'] = '41';
    $this->background['green'] = '42';
    $this->background['yellow'] = '43';
    $this->background['blue'] = '44';
    $this->background['magenta'] = '45';
    $this->background['cyan'] = '46';
    $this->background['light_gray'] = '47';
  }

  /**
   * Return a console colored version of a string
   *
   * @param string $str The string to colorize
   * @param string $color The color to make it
   * @return string
   */
  public function colorize($str, $color)
  {
    $cstr = "\033[" . $this->foreground[$color] . "m";
    $cstr .= $str . "\033[0m";

    return $cstr;
  }

  /**
   * Write a line to the console
   *
   * @param string $line
   * @param string $color
   *
   */
  public function writeLine($line = '', $color = '')
  {
    $line = (empty($color)) ? $line : $this->colorize($line, $color);

    echo $line . PHP_EOL;
  }

  /**
   * Write a horizontal rule
   *
   * @param string $color
   */
  public function writeHR($color = '')
  {
    $line = '';
    foreach (range(0,79) as $char)
      $line .= '-';

    $this->writeLine($line, $color);
  }

  /**
   * Write out the usage banner for this application
   *
   * @param string $app_name;
   * @param string $app_version;
   * @param array $available_commands
   */
  public function printUsageBanner($app_name, $app_version, $available_commands)
  {
    $this->writeLine($app_name . ' ' . $app_version, 'red');
    $this->writeLine();
    $this->writeLine('Usage', 'green');
    $this->writeHR('green');;

    foreach ($available_commands as $command=>$description)
    {
      $line = $this->colorize(str_pad($command, 30), 'yellow');
      $line .= $this->colorize($description, 'light_gray');
      $this->writeLine($line);
    }
    $this->writeLine();
  }
}