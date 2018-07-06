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
defined( 'LGV_ADB_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

if ( !defined( 'LGV_DBF_CATCHER' ) ) {
    define( 'LGV_DBF_CATCHER', 1 );
}
	    
if ( !defined('LGV_ERROR_CATCHER') ) {
    define('LGV_ERROR_CATCHER', 1);
}

require_once(CO_Config::db_class_dir().'/co_main_db_record.class.php');

require_once(CO_Config::badger_shared_class_dir().'/error.class.php');

/***************************************************************************************************************************/
/**
This is the abstract base class for both Badger databases. The bulk of database access, and most of the security, appears in
this class.

The key to security is in the two protected _predicate methods (one for read, and one for write). These analyze the logged-in
user's list of IDs (using the access class' get_security_ids() method, which reloads the security item), and construct an SQL 
predicate for calls that prevent records from being considered that do not match the security profile. This means the records
are never even read into the object from the database. They are excluded by SQL.
 */
abstract class A_CO_DB {
    protected $_pdo_object;
    protected $_existing_record_objects;
    var $access_object;
    var $class_description;
    var $error;
    var $table_name;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This executes an SQL query, using the PDO instance. It uses PDO prepared statements to apply the query.
    
    This is a "bottleneck" method. All access to the database needs to go through here.
    
    \returns any response from the database. true, if it is an "execute only" query that succeeds.
     */
    private function _execute_query(    $in_sql,                ///< This is the SQL portion of the prepared statement.
                                        $in_parameters = NULL,  ///< This is an array of values to be used in the prepared statement.
                                        $exec_only = false      ///< If true, then this means we do not expect a response. Default is false.
                                        ) {
        $ret = NULL;
        $this->error = NULL;
        try {
            if ($exec_only) {
                $ret = $this->_pdo_object->preparedExec($in_sql, $in_parameters);
            } else {
                $ret = $this->_pdo_object->preparedQuery($in_sql, $in_parameters, false);
            }
        } catch (Exception $exception) {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_to_open_security_db,
                                            CO_Lang::$pdo_error_name_failed_to_open_security_db,
                                            CO_Lang::$pdo_error_desc_failed_to_open_security_db,
                                            $exception->getFile(),
                                            $exception->getLine(),
                                            $exception->getMessage());
            $this->_security_db_object = NULL;
        }
        
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This function creates an SQL predicate that limits the query to only those records to which the current logged-in user has read rights.
    
    \returns a string, containing the SQL predicate
     */
    protected function _create_read_security_predicate() {
        if ($this->access_object->god_mode()) {    // God can do everything...
            return '';
        } else {
            $access_ids = $this->access_object->get_security_ids();
            $ret = '((read_security_id=0) OR (read_security_id IS NULL)';
            
            if (isset($access_ids) && is_array($access_ids) && count($access_ids)) {
                foreach ($access_ids as $access_id) {
                    $ret .= ' OR (read_security_id='.intval($access_id).')';
                    $ret .= ' OR (write_security_id='.intval($access_id).')';   // Write security also means that we can read.
                }
            }
        
            $ret .= ')';
        
            // Only God can access God...
            if (($this instanceof CO_Security_DB) && !$this->access_object->god_mode()) {
                $ret .= ' AND (id<>'.intval(CO_Config::god_mode_id()).')';
            }

            return $ret;
        }
    }
    
    /***********************/
    /**
    This function creates an SQL predicate that limits the query to only those records to which the current logged-in user has modification rights.
    
    \returns a string, containing the SQL predicate
     */
    protected function _create_write_security_predicate() {
        if ($this->access_object->god_mode()) {    // God can do everything...
            return '';
        } else {
            $access_ids = $this->access_object->get_security_ids();
            $ret = '0';
            
            if (isset($access_ids) && is_array($access_ids) && count($access_ids)) {
                $ret = '((write_security_id=0) OR (write_security_id IS NULL)';
                
                foreach ($access_ids as $access_id) {
                    $ret .= ' OR (write_security_id='.intval($access_id).')';
                }
        
                $ret .= ')';
        
                // Only God can access God...
                if (($this instanceof CO_Security_DB) && !$this->access_object->god_mode()) {
                    $ret .= ' AND (id<>'.intval(CO_Config::god_mode_id()).')';
                }
            }
        
            return $ret;
        }
    }
    
    /***********************/
    /**
    This creates the appropriate security predicate.
    
    \returns a string, containing the SQL predicate
     */
    protected function _create_security_predicate(  $write = false  ///< This should be true, if we need a write predicate. Default is false.
                                                    ) {
        
        $ret = $write ? $this->_create_write_security_predicate() : $this->_create_read_security_predicate();
        
        return $ret;
    }
    
    /***********************/
    /**
    This creates a new record, based upon the class stored in the database.
    
    \returns a new instance of a subclass of A_CO_DB_Table_Base, loaded with its state.
     */
    public function _instantiate_record(    $in_db_result   ///< This is an associative array, with the results of the row, read from the database.
                                        ) {
        $ret = NULL;
        
        $classname = trim($in_db_result['access_class']);
        $id = intval($in_db_result['id']);
        
        if ($classname && $id) {
            $filename = CO_Config::db_classes_class_dir().'/'.strtolower($classname).'.class.php';
            
            if (!class_exists($classname)) {
                if (!file_exists($filename)) {
                    $filename = NULL;
                    $dir_array = CO_Config::db_classes_extension_class_dir();
                    
                    if (!is_array($dir_array)) {
                        $dir_array = Array($dir_array);
                    }
                
                    foreach ($dir_array as $dir) {
                        $filename = $dir.'/'.strtolower($classname).'.class.php';                
                        if (file_exists($filename)) {
                            require_once($filename);
                            break;
                        } else {
                            $filename = NULL;
                        }
                    }
                } else {
                    require_once($filename);
                }
            }
            
            if (!$filename) {
                $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_class_file_not_found,
                                                CO_Lang::$db_error_name_class_file_not_found,
                                                CO_Lang::$db_error_desc_class_file_not_found,
                                                $classname,
                                                __LINE__,
                                                __METHOD__
                                            );
                return NULL;
            }

            if (class_exists($classname)) { // We make sure that we send references to the same object, if we instantiate it multiple times.

                if (isset($this->_existing_record_objects[$id])) {
                    $ret = $this->_existing_record_objects[$id];
                    if (isset($ret)) {  // We make sure the object jives with what was given us from the DB.
                        // This forces the collection-based classes to reload their children.
                        if (method_exists($ret, 'reload_collection')) {
                            $ret->reload_collection();
                        }
                        $ret->load_from_db($in_db_result);
                    }
                } else {
                    $ret = new $classname($this, $in_db_result);
                }
                
                if (!$ret) {
                    $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_class_not_created,
                                                    CO_Lang::$db_error_name_class_not_created,
                                                    CO_Lang::$db_error_desc_class_not_created,
                                                    $classname,
                                                    __LINE__,
                                                    __METHOD__
                                                );
                    return NULL;
                }
                
                // Make sure we cache this for later.
                $this->_existing_record_objects[$id] = $ret;
            } else {
                $this->error = new LGV_Error(   CO_Lang_Common::$db_error_code_class_not_created,
                                                CO_Lang::$db_error_name_class_not_created,
                                                CO_Lang::$db_error_desc_class_not_created,
                                                $classname,
                                                __LINE__,
                                                __METHOD__
                                            );
            }
        }
        
        return $ret;
    }

    /***********************************************************************************************************************/
    /***********************/
    /**
    The initializer
     */
	public function __construct(    $in_pdo_object,     ///< This is the PDO object used to access the database. It should be initialized and connected at the time this method is called.
	                                $in_access_object   ///< This is the access instance used to handle the login.
                                ) {
        $this->class_description = 'Abstract Base Class for Database -Should never be instantiated.';
        
        $this->access_object = $in_access_object;
        $this->error = NULL;
        $this->table_name = NULL;
        $this->_pdo_object = $in_pdo_object;
        if (isset($this->_pdo_object)) {
            $this->_pdo_object->owner_instance = $this;
        }
        $this->_existing_record_objects = Array();
    }
    
    /***********************/
    /**
    This is an accessor for the query execution method.
    
    \returns any response from the database.
     */
    public function execute_query(  $in_sql,                ///< This is the SQL portion of the prepared statement.
                                    $in_parameters = NULL,  ///< This is an array of values to be used in the prepared statement.
                                    $exec_only = false      ///< If true, then this means we do not expect a response. Default is false.
                                    ) {
        return $this->_execute_query($in_sql, $in_parameters, $exec_only);
    }
    
    /***********************/
    /**
    This is a special method that does not apply a security predicate. It is used to force-reload record instances.
    
    This should ONLY be called from the database reloader functions.
    
    \returns an associative array, containing the "raw" database record response.
     */
    public function get_single_raw_row_by_id(   $in_id,             ///< This is the ID of the record to fetch.
                                                $and_write = false  ///< If this is true, then we need modify permission for the record.
                                            ) {
        $ret = NULL;
        
        $predicate = $this->_create_security_predicate($and_write);
        
        if ($predicate) {
            $predicate .= ' AND ';
        }
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate.'id='.intval($in_id);

        $ret = $this->execute_query($sql, Array());
        
        if (isset($ret) && is_array($ret) && count($ret)) {
            $ret = $ret[0];
        }
        return $ret;
    }
    
    /***********************/
    /**
    This function returns a single record instance, by its unique ID in the table.
    
    This is properly "screened" for security.
    
    \returns a single instance of a subclass of A_CO_DB_Table_Base
     */
    public function get_single_record_by_id(    $in_id,             ///< This is the ID of the record to fetch.
                                                $and_write = false  ///< If this is true, then we need modify permission for the record.
                                            ) {
        $ret = NULL;
        
        $temp = $this->get_multiple_records_by_id(Array($in_id), $and_write);
        
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = $temp[0];
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This function returns one or more record instances, by their unique IDs in the table.
    
    This is properly "screened" for security.
    
    \returns an array, containing one or more instances of a subclass of A_CO_DB_Table_Base (Each instance may be a different class)
     */
    public function get_multiple_records_by_id( $in_id_array,       ///< This is an array of integers, each of which is the ID of a record to fetch.
                                                $and_write = false  ///< If this is true, then we need modify permission for the record.
                                                ) {
        $ret = NULL;
        
        $predicate = $this->_create_security_predicate($and_write);
        
        if ($predicate) {   // If we got a predicate, then we AND it with the rest of the statement.
            $predicate .= ' AND ';
        }
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate.'(';
        $params = Array();
        // Clean the array to make sure they are all integers.
        $id_array = array_map('intval', $in_id_array);
        foreach ($id_array as $id) {
            if (0 < $id) {
                if (0 < count($params)) {
                    $sql .= ' OR ';
                }
                $sql.= '(id=?)';
                array_push($params, $id);
            }
        }
        
        $sql  .= ')';

        $temp = $this->execute_query($sql, $params);
// // Commented out, but useful for debug.
// echo('SQL:<pre>'.htmlspecialchars(print_r($sql, true)).'</pre>');
// echo('PARAMS:<pre>'.htmlspecialchars(print_r($params, true)).'</pre>');
// echo('RESPONSE:<pre>'.htmlspecialchars(print_r($temp, true)).'</pre>');
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = Array();
            foreach ($temp as $result) {
                $result = $this->_instantiate_record($result);
                if (isset($result) && ($result instanceof A_CO_DB_Table_Base)) {
                    $weregood = $and_write ? $result->user_can_write() : $result->user_can_read();
                    // Belt and suspenders. Make sure nothing leaks through.
                    if ($weregood) {
                        $ret[] = $result;
                    }
                }
            }
            usort($ret, function($a, $b){return ($a->id() > $b->id());});
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This returns instances of every readable record in the database readable by the logged-in user (or only "open" records, if no login).
    
    This is properly "screened" for security.
    
    \returns an array, containing one or more instances of a subclass of A_CO_DB_Table_Base (Each instance may be a different class)
     */
    public function get_all_readable_records(   $open_only = false, ///< If true, then we will look for ONLY records with a NULL or 0 read_security_id, even if we are logged in.
                                                $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                            ) {
        $ret = NULL;
        
        $predicate = $open_only ? '((read_security_id=0) OR (read_security_id IS NULL))' : $this->_create_security_predicate();
        
        // No need for an AND, as the predicate is the only qualifier.
        if (!$predicate) {
            $in_this_id = intval($in_this_id);
            if ($in_this_id) {  // We can look for certain IDs in God Mode.
                $predicate = "(read_security_id=$in_this_id)";
                
            } else {
                $predicate = 'true'; // If we are in "God Mode," we could get no predicate, so we just go with "1".
            }
        }
        
        $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate;
        $temp = $this->execute_query($sql, Array());
        if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
            $ret = Array();
            foreach ($temp as $result) {
                if (isset($result) && is_array($result) && count($result)) {
                    $result = $this->_instantiate_record($result);
                    // Belt and suspenders. Make sure nothing leaks through.
                    if ($result && $result->user_can_read()) {
                        array_push($ret, $result);
                    }
                }
            }
            usort($ret, function($a, $b){return ($a->id() > $b->id());});
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This returns instances of every writeable record in the database readable by the logged-in user (cannot be used by non-logged-in-users).
    
    This is properly "screened" for security.
    
    \returns an array, containing one or more instances of a subclass of A_CO_DB_Table_Base (Each instance may be a different class)
     */
    public function get_all_writeable_records(  $in_this_id = NULL  ///< If we are in "god mode," we can look for particular IDs.
                                            ) {
        $ret = NULL;
        
        $access_ids = $this->access_object->get_security_ids();

        // Only logged-in users can write.
        if (isset($access_ids) && is_array($access_ids) && count($access_ids)) {
            $predicate = $this->_create_security_predicate(true);
        
            // No need for an AND, as the predicate is the only qualifier.
            if (!$predicate) {
                $in_this_id = intval($in_this_id);
                if ($in_this_id) {  // We can look for certain IDs in God Mode.
                    $predicate = "(write_security_id=$in_this_id)";
                
                } else {
                    $predicate = 'true'; // If we are in "God Mode," we could get no predicate, so we just go with "1".
                }
            }
        
            $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate;
            $temp = $this->execute_query($sql, Array());
            if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                $ret = Array();
                foreach ($temp as $result) {
                    $result = $this->_instantiate_record($result);
                    // Belt and suspenders. Make sure nothing leaks through.
                    if ($result && $result->user_can_write()) {
                        array_push($ret, $result);
                    }
                }
                usort($ret, function($a, $b){return ($a->id() > $b->id());});
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This "locks" a given record by setting its read_security_id to -2.
    
    \returns an integer; the previous contents of the read_security_id column.
     */
    public function lock_record(    $in_record_id   ///< This is the unique ID of the record to be "locked."
                                ) {
        $ret = 0;
        
        $predicate = $this->_create_security_predicate(true);

        if ($predicate) {
            $predicate .= ' AND ';
        }
        
        $in_record_id = intval($in_record_id);  // Make sure we are an integer.
        
        if (0 < $in_record_id) {
            // First, we look for a record with our ID, and for which we have write permission, and we get the original Read ID.
            $sql = 'SELECT read_security_id FROM '.$this->table_name.' WHERE '.$predicate.'id='.$in_record_id;

            $temp = $this->execute_query($sql, Array());
            
            if (isset($temp) && $temp && is_array($temp) && (1 == count($temp)) ) { // If we  got a record, then we'll be updating it.
                $ret = intval($temp[0]);    // Get the original value.
                
                $sql = 'UPDATE '.$this->table_name.' SET read_security_id=-2 WHERE id='.$in_record_id;
        
                $temp = $this->execute_query($sql, Array());
                
                if ($this->error) { // If we had an error, we return squat.
                    $ret = 0;
                }
            }
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This updates or creates a database record (row), based on an associative array of parameters passed in.
    
    If the 'id' column is 0 or NULL, then this will be a new record.
    
    This is properly "screened" for security.
    
    \returns true (Successful update), or the integer ID of a new record.
     */
    public function write_record(   $params_associative_array   ///< This is an associative array of parameters, where the key is the database column name.
                                ) {
        $ret = false;
        if (isset($params_associative_array) && is_array($params_associative_array) && count($params_associative_array)) {
            $access_ids = $this->access_object->get_security_ids();
            $params_associative_array['last_access'] = date('Y-m-d H:i:s');
            if (isset($access_ids) && is_array($access_ids) && count($access_ids)) {
                $predicate = $this->_create_security_predicate(true);
        
                if ($predicate) {
                    $predicate .= ' AND ';
                }
            
                $id = isset($params_associative_array['id']) ? intval($params_associative_array['id']) : 0;  // We extract  the ID from the fields, or assume a new record.
                
                if (0 < $id) {
                    // First, we look for a record with our ID, and for which we have write permission.
                    $sql = 'SELECT * FROM '.$this->table_name.' WHERE '.$predicate.'id='.$id;

                    $temp = $this->execute_query($sql, Array());
                
                    if (isset($temp) && $temp && is_array($temp) && (1 == count($temp)) ) { // If we  got a record, then we'll be updating it.
                        $sql = 'UPDATE '.$this->table_name.'';
                        unset($params_associative_array['id']); // We remove the ID parameter. That can't be changed.
                        
                        $params = array_values($params_associative_array);
                        $keys = array_keys($params_associative_array);
                        $set_sql = ''.implode('=?,', $keys).'=?';
                    
                        $sql .= ' SET '.$set_sql.' WHERE ('.$predicate.'id='.$id.')';
                        $this->execute_query($sql, $params, true);
                        if (!$this->error) {    // Make sure that we update the access time on cached objects.
                            if (isset($this->_existing_record_objects[$id])) {
                                $this->_existing_record_objects[$id]->last_access = strtotime($params_associative_array['last_access']);
                            }
                            $ret = true;
                        }
                    } else {
                        $sql = 'SELECT * FROM '.$this->table_name.' WHERE id='.$id; // Look for a record with the proposed ID (assume we don't have permission).

                        $temp = $this->execute_query($sql, Array());
                        
                        if (isset($temp) && $temp && is_array($temp) && count($temp)) { // If we  got a record, then we're aborting, as we tried to change an existing rcord.
                            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_write_attempt,
                                                            CO_Lang::$pdo_error_name_illegal_write_attempt,
                                                            CO_Lang::$pdo_error_desc_illegal_write_attempt);
                        }
                    }
                } else {
                    $sql = 'SELECT * FROM '.$this->table_name.' WHERE id=1'; // We simply get the template.

                    $temp = $this->execute_query($sql, Array());
                    
                    if (isset($temp) && $temp && is_array($temp) && count($temp) ) {
                        $sql = 'INSERT INTO '.$this->table_name.'';
                        unset($params_associative_array['id']);
                        
                        // If there is no read ID specified, it becomes public.
                        if (!isset($params_associative_array['read_security_id'])) {
                            $params_associative_array['read_security_id'] = 0;
                        }
                        
                        // If there is no write ID specified, then we simply take the first one off the list.
                        if (!isset($params_associative_array['write_security_id'])) {
                            $params_associative_array['write_security_id'] = $access_ids[1];
                        }
                        
                        foreach ($params_associative_array as $key => $value) {
                            if (isset($temp[$key])) {
                                $temp[$key] = isset($value) ? $value : NULL;
                            }
                        }
                        
                        $keys = array_keys($params_associative_array);
                    
                        $keys_sql = '('.implode(',', $keys).')';
                        $values_sql = '('.implode(',', array_map(function($in){return '?';}, $keys)).') RETURNING id;';
                        
                        $sql .= " $keys_sql VALUES $values_sql";
                        $this->execute_query($sql, array_values($params_associative_array), true);
                        
                        if (!$this->error) {
                            // Get the ID of the new row we just created.
                            $ret = $this->_pdo_object->last_insert;
                        }
                    } else {
                        $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_write_attempt,
                                                        CO_Lang::$pdo_error_name_illegal_write_attempt,
                                                        CO_Lang::$pdo_error_desc_illegal_write_attempt);
                    }
                }
            }
        }
                
        return $ret;
    }
    
    /***********************/
    /**
    This deletes a record, based on its unique ID in the table. The logged-in user must have write permission for the record.
    
    This double-checks after the delete, just to make sure the record was, indeed, deleted.
    
    This is properly "screened" for security.
    
    \returns true if the deletion was successful.
     */
    public function delete_record(  $id
                                ) {
        $ret = false;
        
        $id = intval($id);
        $predicate = $this->_create_security_predicate(true);
        
        if ($predicate) {
            $predicate .= ' AND ';
        }

        // First, make sure we have write permission for this record, and that the record exists.
        $sql = 'SELECT id FROM '.$this->table_name.' WHERE ('.$predicate.'id='.$id.')';
        $temp = $this->execute_query($sql);
        
        if (!$this->error && isset($temp) && $temp && is_array($temp) && (1 == count($temp))) {
            $sql = 'DELETE FROM '.$this->table_name.' WHERE ('.$predicate.'id='.$id.')';
            $temp = $this->execute_query($sql, Array(), true);  // We call this as an "execute-only" query.
            if (!$temp || $this->error) {
                $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_delete_attempt,
                                                CO_Lang::$pdo_error_name_failed_delete_attempt,
                                                CO_Lang::$pdo_error_desc_failed_delete_attempt);
            } else {
                // Make sure she's dead, Jim. We do an open-ended check.
                $sql = 'SELECT id FROM '.$this->table_name.' WHERE id='.$id;
                $temp = $this->execute_query($sql);
                if (!$this->error && isset($temp) && is_array($temp) && (0 == count($temp))) {
                    // Make sure that we also remove it from our cache.
                    if (isset($this->_existing_record_objects[$id])) {
                        $this->_existing_record_objects[$id]->danger_will_robinson_danger_clear_id();
                        unset($this->_existing_record_objects[$id]);
                    }
                        
                    $ret = true;
                } else {
                    $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_failed_delete_attempt,
                                                    CO_Lang::$pdo_error_name_failed_delete_attempt,
                                                    CO_Lang::$pdo_error_desc_failed_delete_attempt);
                }
            }
        } else {
            $this->error = new LGV_Error(   CO_Lang_Common::$pdo_error_code_illegal_delete_attempt,
                                            CO_Lang::$pdo_error_name_illegal_delete_attempt,
                                            CO_Lang::$pdo_error_desc_illegal_delete_attempt);
        }
        
        return $ret;
    }
};
