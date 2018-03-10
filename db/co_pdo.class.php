<?php
/**
    \file s_co_pdo.class.php
    
    \brief Class that implements a simplified connection to the PHP PDO toolkit.
*/
defined( 'LGV_DB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/**
    \brief This class provides a genericized interface to the <a href="http://us.php.net/pdo">PHP PDO</a> toolkit. It is a completely static class.
 */
class CO_PDO {
	/// \brief Internal PDO object
	private $pdo = null;

	/// \brief Default fetch mode for internal PDOStatements
	private $fetchMode = PDO::FETCH_ASSOC;

	/**
		\brief Initializes connection param class members.
		
		Must be called BEFORE any attempts to connect to or query a database.
		
		Will destroy previous connection (if one exists).
	*/
	public function __construct(
								$driver,			///< database server type (ex: 'mysql')
								$host,				///< database server host
								$database,			///< database name
								$user = null,		///< user, optional
								$password = null,	///< password, optional
								$charset = null		///< connection charset, optional
								) {
		$this->pdo = null;
		
        $dsn = $driver . ':host=' . $host . ';dbname=' . $database;
        $this->pdo = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        if (strlen($charset) > 0) {
            self::preparedExec('SET NAMES :charset', array(':charset' => $charset));
        }
	}

	/**
		\brief Wrapper for preparing and executing a PDOStatement that returns a resultset
		e.g. SELECT SQL statements.

		Returns a multidimensional array depending on internal fetch mode setting ($this->fetchMode)
		See PDO documentation about prepared queries.

		Fetching key pairs- when $fetchKeyPair is set to TRUE, it will force the returned
		array to be a one-dimensional array indexed on the first column in the query.
		Note- query may contain only two columns or an exception/error is thrown.
		See PDO::PDO::FETCH_KEY_PAIR for more details

		\returns associative array of results.
		\throws Exception	 thrown if internal PDO exception is thrown
	*/
	public function preparedQuery(
										$sql,					///< same as kind provided to PDO::prepare()
										$params = array(),		///< same as kind provided to PDO::prepare()
										$fetchKeyPair = false	///< See description in method documentation
										) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode($this->fetchMode);
        $stmt->execute($params);

        if ($fetchKeyPair) {
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } else {
            return $stmt->fetchAll();
		}
	}

	/**
		\brief Wrapper for PDO::lastInsertId()
		
		\returns the ID of the last INSERT
		\throws Exception	 thrown if internal PDO object not instantiated
	*/
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
};
