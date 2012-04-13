<?php
/**
 * Archive Connection Class
 *
 * This wrapper works with a driver class to pass information from the Archive to
 * VuFind.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  Archive_Drivers
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */

/**
 * Archive Connection Class
 *
 * This wrapper works with a driver class to pass information from the Archive to
 * VuFind.
 *
 * @category VuFind
 * @package  Archive_Drivers
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class ArchiveConnection
{
    /**
     * A boolean value that defines whether a connection has been successfully
     * made.
     *
     * @access public
     * @var    bool
     */
    public $status = false;

    /**
     * The object of the appropriate driver.
     *
     * @access private
     * @var    object
     */
    public $driver;

    /**
     * Constructor
     *
     * This is responsible for instantiating the driver that has been specified.
     *
     * @param string $driver The name of the driver to load.
     *
     * @access public
     */
    public function __construct($driver)
    {
        global $configArray;
        $path = "{$configArray['Site']['local']}/Drivers/{$driver}.php";
        if (is_readable($path)) {
            include_once $path;

            try {
                $this->driver = new $driver;
            } catch (PDOException $e) {
                throw $e;
            }

            $this->status = true;
        }
    }

    /**
     * Default method -- pass along calls to the driver if available; return
     * false otherwise.  This allows custom functions to be implemented in
     * the driver without constant modification to the connection class.
     *
     * @param string $methodName The name of the called method.
     * @param array  $params     Array of passed parameters.
     *
     * @return mixed             Varies by method (false if undefined method)
     * @access public
     */
    public function __call($methodName, $params)
    {
        $method = array($this->driver, $methodName);
        if (is_callable($method)) {
            return call_user_func_array($method, $params);
        }
        return false;
    }
}

?>
