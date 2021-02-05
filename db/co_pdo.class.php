<?php
/***************************************************************************************************************************/
/**
    Badger Hardened Baseline Database Component
    
    © Copyright 2021, The Great Rift Valley Software Company
    
    LICENSE:
    
    MIT License
    
    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
    modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
    IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
    CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

    The Great Rift Valley Software Company: https://riftvalleysoftware.com
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
	public function __construct(    $driver,			///< database server type (ex: 'mysql')
                                    $host,				///< database server host
                                    $database,			///< database name
                                    $user = NULL,		///< user, optional
                                    $password = NULL,	///< password, optional
                                    $charset = NULL		///< connection charset, optional
								) {
        
        $this->class_description = 'A class for managing PDO access to the databases.';

		$this->_pdo = NULL;
		$this->driver_type = $driver;
		$this->last_insert = NULL;
		
        $dsn = $driver . ':host=' . $host . ';dbname=' . $database ;
		try {
            $this->_pdo = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            if (strlen($charset) > 0) {
                self::preparedExec('SET NAMES :charset', array(':charset' => $charset), false);
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
	public function preparedExec(   $sql,				///< same as kind provided to PDO::prepare()
								    $params = array()	///< same as kind provided to PDO::prepare()
						        )
	{
		$this->last_insert = NULL;
		try {
			if ('pgsql' == $this->driver_type) {
			    if (strpos($sql, 'RETURNING id;')) {
			        $response = $this->preparedQuery($sql, $params);
                    $this->last_insert = intval($response[0]['id']);
			        return true;
			    }
			}
			
			$sql = str_replace(' RETURNING id', '', $sql);
		    // This represents a potential MASSIVE security, performnce and legal issue. This should ONLY be used for debugging!
		    if (method_exists('CO_Config', 'call_low_level_log_handler_function')) {
			    CO_Config::call_low_level_log_handler_function(isset($this->owner_instance) ? $this->owner_instance->access_object->get_login_id() : 0, $sql, $params);
			}
            $this->_pdo->beginTransaction(); 
            $stmt = $this->_pdo->prepare($sql);
            $stmt->execute($params);
			if ('pgsql' != $this->driver_type) {
                $this->last_insert = $this->_pdo->lastInsertId();
            }
            $this->_pdo->commit();
		
            return true;
		} catch (PDOException $exception) {
		    $this->last_insert = NULL;
            $this->_pdo->rollback();
			throw new Exception(__METHOD__ . '() ' . $exception->getMessage());
		}
		
        return false;
	}

    /***********************/
	/**
		\brief Wrapper for preparing and executing a PDOStatement that returns a resultset
		e.g. SELECT SQL statements.

		Returns a multidimensional array depending on internal fetch mode setting ($this->fetchMode)
		See PDO documentation about prepared queries.

		Fetching key pairs- when $fetchKeyPair is set to true, it will force the returned
		array to be a one-dimensional array indexed on the first column in the query.
		Note- query may contain only two columns or an exception/error is thrown.
		See PDO::PDO::FETCH_KEY_PAIR for more details

		\returns associative array of results.
		\throws Exception	 thrown if internal PDO exception is thrown
	*/
	public function preparedQuery(  $sql,					///< same as kind provided to PDO::prepare()
									$params = array(),		///< same as kind provided to PDO::prepare()
									$fetchKeyPair = false   ///< See description in method documentation
								) {
		$this->last_insert = NULL;
		try {
		    // This represents a potential MASSIVE security, performnce and legal issue. This should ONLY be used for debugging!
		    if (method_exists('CO_Config', 'call_low_level_log_handler_function')) {
			    CO_Config::call_low_level_log_handler_function(isset($this->owner_instance) ? $this->owner_instance->access_object->get_login_id() : 0, $sql, $params);
			}
            $this->_pdo->beginTransaction(); 
            $stmt = $this->_pdo->prepare($sql);
            $stmt->setFetchMode($this->fetchMode);
            $stmt->execute($params);
            $this->_pdo->commit();
            
            $ret = NULL;
            
            if ($fetchKeyPair) {
                $ret = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } else {
                $ret = $stmt->fetchAll();
            }
            
            return $ret;
		} catch (PDOException $exception) {
		    $this->last_insert = NULL;
            $this->_pdo->rollback();
			throw new Exception(__METHOD__ . '() ' . $exception->getMessage());
		}
		
        return false;
	}
};
