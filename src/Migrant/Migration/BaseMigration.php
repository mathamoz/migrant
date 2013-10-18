<?php
namespace Migrant\Migration;

use Migrant\Database\Database;

class BaseMigration
{
  protected $config;
  protected $db;

  public function __construct($config)
  {
    $this->config = $config;
    $this->db = new Database($config);
  }

  public function up()
  {

  }

  public function down()
  {

  }

  public function run_sql($sql)
  {
    try
    {
      $result = $this->db->execute($sql);
      return ['success' => true, 'results' => $result];
    } catch (\PDOException $e) {
      return ['success' => false, 'message' => 'Migration Failed! ' . $e->getMessage()];
    }
  }
}