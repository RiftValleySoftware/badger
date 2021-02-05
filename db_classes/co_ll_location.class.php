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
defined( 'LGV_DBF_CATCHER' ) or die ( 'Cannot Execute Directly' );	// Makes sure that this file is in the correct context.

require_once(CO_Config::db_class_dir().'/co_main_db_record.class.php');

/***************************************************************************************************************************/
/**
This is a specialization of the basic data class, implementing the long/lat fields (built into the table structure, but unused by base classes).

It has a "fuzz factor" built in. This is because some organizations, for reasons of privacy or law, don't want exact locations disclosed.

You can define a "fuzz factor" of how many kilometers you want as a "fuzzy circle" around the point.
 */
class CO_LL_Location extends CO_Main_DB_Record {
    protected $_longitude;
    protected $_latitude;
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    This is called to populate the object fields for this class with default values. These use the SQL table tags.
    
    This should be subclassed, and the parent should be called before applying specific instance properties.
    
    This method overloads (and calls) the base class method.
    
    \returns An associative array, simulating a database read.
     */
    protected function _default_setup() {
        $default_setup = parent::_default_setup();
        $default_setup['longitude'] = (NULL != $this->_longitude) ? $this->_longitude : 0;
        $default_setup['latitude'] = (NULL != $this->_latitude) ? $this->_latitude : 0;
        
        return $default_setup;
    }
    
    /***********************/
    /**
    This builds up the basic section of the instance database record. It should be overloaded, and the parent called before adding new fields.
    
    This method overloads (and calls) the base class method.
    
    \returns an associative array, in database record form.
     */
    protected function _build_parameter_array() {
        $ret = parent::_build_parameter_array();
        
        $ret['longitude'] = $this->_longitude;
        $ret['latitude'] = $this->_latitude;
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns a floating-point number, with the Km per degree long/lat (Changes for different latitudes, and is only good for the immediate vicinity).
     */
    protected function _km_per_degree() {
        // We do an average. Take four points in a "cross" around the center, then average them. We go out ten Km, so there's some distance.
        $spot0 = Array('longitude' => $this->_longitude, 'latitude' => $this->_latitude);
        $spot1 = Array('longitude' => $this->_longitude + 10.0, 'latitude' => $this->_latitude);
        $spot2 = Array('longitude' => $this->_longitude - 10.0, 'latitude' => $this->_latitude);
        $spot3 = Array('longitude' => $this->_longitude, 'latitude' => $this->_latitude + 10.0);
        $spot4 = Array('longitude' => $this->_longitude, 'latitude' => $this->_latitude - 10.0);
        $distance1 = abs(CO_Main_Data_DB::get_accurate_distance($spot0['latitude'], $spot0['longitude'], $spot1['latitude'], $spot1['longitude']));
        $distance2 = abs(CO_Main_Data_DB::get_accurate_distance($spot0['latitude'], $spot0['longitude'], $spot2['latitude'], $spot2['longitude']));
        $distance3 = abs(CO_Main_Data_DB::get_accurate_distance($spot0['latitude'], $spot0['longitude'], $spot3['latitude'], $spot3['longitude']));
        $distance4 = abs(CO_Main_Data_DB::get_accurate_distance($spot0['latitude'], $spot0['longitude'], $spot4['latitude'], $spot4['longitude']));
        
        return ($distance1 + $distance2 + $distance3 + $distance4) / 40.0;
    }
    
    /***********************/
    /**
    \returns an associative array, with a "fuzzed" long/lat (if there is no fuzz factor, it is the raw long/lat).
     */
    protected function _fuzz_me() {
        $ret = Array('longitude' => $this->_longitude, 'latitude' => $this->_latitude);
        
        $fuzz_factor = $this->fuzz_factor();
        if (0 < $fuzz_factor) {
            // The big number gives it lots of fuzz.
            $long_offset = function_exists('random_int') ? random_int(0, 100000 * ($fuzz_factor)) / 100000.0 : rand(0, 100000 * ($fuzz_factor)) / 100000.0;
            $lat_offset = function_exists('random_int') ? random_int(0, 100000 * ($fuzz_factor)) / 100000.0 : rand(0, 100000 * ($fuzz_factor)) / 100000.0;
            
            // Convert the fuziness to degrees.
            $km_per_degree = $this->_km_per_degree();
            $long_offset /= $km_per_degree;
            $lat_offset /= $km_per_degree;

            // This determines the direction we go. We make each axis an independent rand().
            $long_offset *= (rand(0, 9) < 5 ? -1.0 : 1.0);
            $lat_offset *= (rand(0, 9) < 5 ? -1.0 : 1.0);
            
            $ret['longitude'] += $long_offset;
            $ret['latitude'] += $lat_offset;
        }
        return $ret;
    }
    
    /***********************************************************************************************************************/
    /***********************/
    /**
    Constructor (Initializer)
     */
	public function __construct(    $in_db_object = NULL,               ///< The database object for this instance.
	                                $in_db_result = NULL,               ///< The database row for this instance (associative array, with database keys).
	                                $in_owner_id = NULL,                ///< The ID of the object (in the database) that "owns" this instance.
	                                $in_tags_array = NULL,              ///< An array of strings, up to ten elements long, for the tags.      
	                                $in_longitude = NULL,               ///< An initial longitude value.
	                                $in_latitude = NULL,                ///< An initial latitude value.
	                                $in_fuzz_factor = NULL,             ///< An initial "fuzz factor" value.
	                                $in_can_see_through_the_fuzz = NULL ///< This is a security token for being able to see the value as a raw value (unfuzzed).
                                ) {
        parent::__construct($in_db_object, $in_db_result, $in_owner_id, $in_tags_array);
        
        if (NULL != $in_longitude) {
            $this->_longitude = $in_longitude;
        }
        
        if (NULL != $in_latitude) {
            $this->_latitude = $in_latitude;
        }
        
        if ((NULL != $in_fuzz_factor) && (0 != $in_fuzz_factor)) {
            $this->context['fuzz_factor'] = $in_fuzz_factor;
        } elseif (!isset($this->context['fuzz_factor']) || !$this->context['fuzz_factor']) {
            $this->context['fuzz_factor'] = 0;
        }
        
        if (NULL != $in_can_see_through_the_fuzz) {
            $this->context['can_see_through_the_fuzz'] = $in_can_see_through_the_fuzz;
        }
    }

    /***********************/
    /**
    This function sets up this instance, according to the DB-formatted associative array passed in.
    
    \returns true, if the instance was able to set itself up to the provided array.
     */
    public function load_from_db(   $in_db_result   ///< This is an associative array, formatted as a database row response.
                                    ) {
        $ret = parent::load_from_db($in_db_result);
        
        if ($ret) {
            $this->class_description = 'A basic class for long/lat locations.';
        
            if ($this->_db_object) {
                if (isset($in_db_result['longitude'])) {
                    $this->_longitude = doubleval($in_db_result['longitude']);
                }
        
                if (isset($in_db_result['latitude'])) {
                    $this->_latitude = doubleval($in_db_result['latitude']);
                }
            }
        
            $ll_string = ((NULL != $this->_longitude) && (NULL != $this->_latitude)) ? "($this->_longitude, $this->_latitude)" : '';
        
            $this->class_description = "Generic longitude/latitude Class.";
            $this->instance_description = isset($this->name) && $this->name ? "$this->name $ll_string" : $ll_string;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This is a "trigger" to update the database with the current instance state.
    
    This checks to make sure the user has write permission before saving.
    
    \returns true, if a DB update was successful.
     */
    public function update_db() {
        $ret = parent::update_db();
        if ($ret) {
            $ll_string = ((NULL != $this->_longitude) && (NULL != $this->_latitude)) ? "($this->_longitude, $this->_latitude)" : '';
        
            $this->instance_description = isset($this->name) && $this->name ? "$this->name $ll_string" : $ll_string;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter for longitude.
    
    \returns true, if the save was successful.
     */
    public function set_longitude(  $in_new_value
                                    ) {
        $ret = false;
        
        if (isset($in_new_value) && $this->user_can_write()) {
            $this->_longitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Setter for latitude.
    
    \returns true, if the save was successful.
     */
    public function set_latitude(   $in_new_value
                                    ) {
        $ret = false;
        
        if (isset($in_new_value) && $this->user_can_write()) {
            $this->_latitude = floatval($in_new_value);
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Getter for fuzz factor.
    
    \returns the fuzz factor, as a float. If it is not set, then it is zero.
     */
     public function fuzz_factor() {
        return isset($this->context['fuzz_factor']) ? abs(floatval($this->context['fuzz_factor'])) : 0.0;
    }

    /***********************/
    /**
    \returns true, if the instance has a "fuzz factor."
     */
    public function is_fuzzy() {
        return 0.0 < $this->fuzz_factor();
    }
    
    /***********************/
    /**
    Setter for fuzz factor.
    
    \returns true, if the save was successful.
     */
    public function set_fuzz_factor(   $in_new_value    ///< The new value must be a positive floating-point value over (and including) 0. If zero, the factor is deleted.
                                    ) {
        $ret = false;
        
        $in_new_value = abs(floatval($in_new_value));
        
        if ($this->user_can_write()) {
            if (0 == $in_new_value) {
                unset($this->context['fuzz_factor']);
            } else {
                $this->context['fuzz_factor'] = $in_new_value;
            }
            
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    This returns the longitude. However, if the user is not logged in, or doesn't have read rights (which shouldn't happen, anyway), they will only get the "fuzzed" version.
    
    \returns The current longitude value.
     */
    public function raw_longitude() {
        if ($this->i_can_see_clearly_now()) {
            return $this->_longitude;
        } else {
            return $this->longitude();
        }
    }
    
    /***********************/
    /**
    This returns the latitude. However, if the user is not logged in, or doesn't have read rights (which shouldn't happen, anyway), they will only get the "fuzzed" version.
    
    \returns The current longitude value.
     */
    public function raw_latitude() {
        if ($this->i_can_see_clearly_now()) {
            return $this->_latitude;
        } else {
            return $this->latitude();
        }
    }
    
    /***********************/
    /**
    This returns the longitude, with any "fuzz factor" applied.
    
    \returns The current longitude value.
     */
    public function longitude() {
        return $this->_fuzz_me()['longitude'];
    }
    
    /***********************/
    /**
    This returns the longitude, with any "fuzz factor" applied.
    
    \returns The current latitude value.
     */
    public function latitude() {
        return $this->_fuzz_me()['latitude'];
    }
    
    /***********************/
    /**
    Setter for a security ID token that can see past the fuzz factor.
    
    \returns true, if the save was successful.
     */
    public function set_can_see_through_the_fuzz(   $in_id  ///< The ID to set. If 0 or NULL, the value is removed.
                                                ) {
        $ret = false;
        
        if ($this->user_can_write()) {
            $in_id = intval($in_id);
            
            if (0 == $in_id) {
                unset($this->context['can_see_through_the_fuzz']);
            } else {
                $ids = $this->get_access_object()->get_security_ids();
                
                $in_id = (in_array($in_id, $ids) || $this->get_access_object()->god_mode()) ? $in_id : 0;
                
                if ($in_id) {
                    $this->context['can_see_through_the_fuzz'] = $in_id;
                } else {
                    unset($this->context['can_see_through_the_fuzz']);
                }
            }
            
            $ret = $this->update_db();
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    Getter for a security ID token that can see past the fuzz factor.
    
    \returns an integer, with the ID (as long as we can read it). 0 if we don't have permission for the ID, or there is none.
     */
    public function can_see_through_the_fuzz() {
        $ret = 0;
        
        $ids = $this->get_access_object()->get_security_ids();

        $my_see_item = intval($this->write_security_id);
        
        $the_id = isset($this->context['can_see_through_the_fuzz']) ? intval($this->context['can_see_through_the_fuzz']) : 0;
        
        if (isset($ids) && is_array($ids) && count($ids)) {
            $ret = in_array($the_id, $ids) ? $the_id : 0;
        }
        
        return $ret;
    }
    
    /***********************/
    /**
    \returns true, if current user has the ability to see the raw values.
     */
    public function i_can_see_clearly_now() {
        $ret = !$this->is_fuzzy() || $this->user_can_write();  // If we aren't fuzzed, then, no problem. Peep away. Writers can see.
        
        if (!$ret && $this->get_access_object()->security_db_available()) { // Only logged-in users get to see clearly.
            if (!$ret && isset($this->context['can_see_through_the_fuzz'])) {
                $ids = $this->get_access_object()->get_security_ids();
            
                $my_see_item = intval($this->context['can_see_through_the_fuzz']);
                if (isset($ids) && is_array($ids) && count($ids)) {
                    $ret = in_array($my_see_item, $ids);
                }
            }
        }
        return $ret;
    }
};
