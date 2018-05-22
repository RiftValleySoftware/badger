<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2018, Little Green Viper Software Development LLC.
    
    This code is proprietary and confidential code, 
    It is NOT to be reused or combined into any application,
    unless done so, specifically under written license from Little Green Viper Software Development LLC.

    Little Green Viper Software Development: https://littlegreenviper.com
*/
defined( 'LGV_DB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

/***************************************************************************************************************************/
/**
    \brief This class provides a genericized interface to the <a href="http://us.php.net/pdo">PHP PDO</a> toolkit.
 */
class CO_PDO {
	/// \brief Internal PDO object
	private $_pdo = NULL;
	/// \brief The type of PDO driver we are configured for.
	var $driver_type = NULL;
	/// \brief A simple description of this class.
	var $class_description = NULL;
	/// \brief This holds the integer ID of the last AUTO_INCREMENT insert.
	var $last_insert = NULL;
	/// \brief This is the instance of A_CO_DB that "owns" this instance. We can use this for auditing.
    var $owner_instance = NULL;
    
	/// \brief Default fetch mode for internal PDOStatements
	private $fetchMode = PDO::FETCH_ASSOC;

    /***********************************************************************************************************************/
    /***********************/
	/**
		\brief Initializes connection param class members.
		
		Must be called BEFORE any attempts to connect to or query a database.
		
		Will destroy previous connection (if one exists).
	*/
	public function __construct(
								$driver,			///< database server type (ex: 'mysql')
								$host,				///< database server host
								$database,			///< database name
								$user = NULL,		///< user, optional
								$password = NULL,	///< password, optional
								$charset = NULL		///< connection charset, optional
								) {
        
        $this->class_description = 'A class for managing PDO access to the databases.';

		$this->_pdo = NULL;
		$this->driver_type = $driver;
		
        $dsn = $driver . ':host=' . $host . ';dbname=' . $database ;
		try {
            $this->_pdo = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
            if (strlen($charset) > 0) {
                self::preparedExec('SET NAMES :charset', array(':charset' => $charset), FALSE);
            }
        } catch (PDOException $exception) {
			throw new Exception(__METHOD__ . '() ' . $exception->getMessage());
        }
	}

    /***********************/
	/**
		\brief Wrapper for preparing and executing a PDOStatement that does not return a resultset
		e.g. INSERT or UPDATE SQL statements

		See PDO documentation about prepared queries.
		
		If there isn't already a database connection, it will "lazy load" the connection.
		
		\throws Exception	 thrown if internal PDO exception is thrown
		\returns true if execution is successful.
	*/
	public function preparedExec(
								    $sql,				///< same as kind provided to PDO::prepare()
								    $params = array()	///< same as kind provided to PDO::prepare()
						        )
	{
		try {
			if ('pgsql' == $this->driver_type) {
			    if (strpos($sql, 'RETURNING id;')) {
			        $response = $this->preparedQuery($sql, $params);
                    $this->last_insert = intval($response[0]['id']);
			        return TRUE;
			    }
			}
			
			$sql = str_replace(' RETURNING id', '', $sql);
// echo('SQL:<pre>'.htmlspecialchars(print_r($sql, true)).'</pre>');
// echo('PARAMS:<pre>'.htmlspecialchars(print_r($params, true)).'</pre>');
            $stmt = $this->_pdo->prepare($sql);
            $this->_pdo->beginTransaction(); 
            $stmt->execute($params);
			if ('pgsql' != $this->driver_type) {
                $this->last_insert = $this->_pdo->lastInsertId();
            }
            $this->_pdo->commit();
		
            return TRUE;
		} catch (PDOException $exception) {
			throw new Exception(__METHOD__ . '() ' . $exception->getMessage());
		}
		
        return FALSE;
	}

    /***********************/
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
										$fetchKeyPair = FALSE   ///< See description in method documentation
										) {
		try {
// echo('SQL:<pre>'.htmlspecialchars(print_r($sql, true)).'</pre>');
// echo('PARAMS:<pre>'.htmlspecialchars(print_r($params, true)).'</pre>');
            $stmt = $this->_pdo->prepare($sql);
            $stmt->setFetchMode($this->fetchMode);
            $this->_pdo->beginTransaction(); 
            $stmt->execute($params);
            $this->_pdo->commit();
            
            $ret = NULL;
            
            if ($fetchKeyPair) {
                $ret = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } else {
                $ret = $stmt->fetchAll();
            }

// echo('RESULT:<pre>'.htmlspecialchars(print_r($ret, true)).'</pre>');
            
            return $ret;
		} catch (PDOException $exception) {
			throw new Exception(__METHOD__ . '() ' . $exception->getMessage());
		}
		
        return FALSE;
	}
};
