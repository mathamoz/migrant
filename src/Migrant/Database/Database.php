<?php
namespace Migrant\Database;

use \PDO;

class Database
{
  protected $pdo;
  protected $config;

  /**
   * Setup the db access class and connect to the database
   *
   * @param array $config
   */
  public function __construct($config)
  {
    $this->config = $config;

    $this->connect();
  }

  /**
   * Connect to the database and store the connection to the class
   *
   */
  private function connect()
  {
    $db = $this->config['database'];
    $this->pdo = new \PDO("mysql:host={$db['server']};dbname={$db['dbname']}", $db['user'], $db['password']);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  /**
   * Close the connection to the database
   *
   */
  private function close()
  {
    $this->pdo = null;
  }

  /**
   * Close the SQL connection
   *
   */
  public function __sleep()
  {
    $this->close();
  }

  /**
   * Reconnect to the database
   *
   */
  public function __wakeup()
  {
    $this->connect();
  }

  /**
   * Execute a query
   *
   * @param string $sql The SQL statement to execute
   * @param array $params Optional array of parameters for the query
   * @return array Array of result rows
   */
  public function query($sql, Array $params = [])
  {
    $res = $this->pdo->prepare($sql);
    $res->execute($params);


    $rows = $res->fetchAll(PDO::FETCH_ASSOC);

    $results = [];
    foreach ($rows as &$row) {
      $results[] = $row;
    }

    return $results;
  }

  /**
   * Insert values into the database
   *
   * @param string $sql The SQL statement to execute
   * @param array $params Optional array of parameters for the insert
   * @return array
   */
  public function insert($sql, Array $params = [])
  {
    return $this->execute($sql, $params);
  }

  /**
   * Update a row in the database
   *
   * @param string $sql The SQL statement to execute
   * @param array $params Array of parameters for the update
   * @return array
   */
  public function update($sql, Array $params)
  {
    return $this->execute($sql, $params);
  }

  /**
   * Delete from the database
   *
   * @param string $sql The delete statement to execute
   * @param array $params Optional array of the parameters to the delete
   * @return array
   */
  public function delete($sql, Array $params = [])
  {
    return $this->execute($sql, $params);
  }

  /**
   * Execute a query and return the result
   *
   * @param string $sql The SQL statement to execute
   * @param array $params Optional array of parameters for the query
   * @return array
   */
  public function execute($sql, Array $params = [])
  {
    $res = $this->pdo->prepare($sql);
    $res->execute($params);
  }
}

?>