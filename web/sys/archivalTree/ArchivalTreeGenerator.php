<?php
/**
 * Archival Tree Generator
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  ArchivalTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */

/**
 * Archival Tree Generator
 *
 * This is a base helper class for producing archival Trees.
 *
 * @category VuFind
 * @package  ArchivalTreeGenerator
 * @author   Luke O'Sullivan <l.osullivan@swansea.ac.uk>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_authentication_handler Wiki
 */
class ArchivalTreeGenerator
{
    /**
     * Constructor. Loads the record Driver.
     *
     * @param object $recordDriver A Record Driver Object
     *
     * @access public
     */
    public function __construct($recordDriver)
    {
        $this->recordDriver = $recordDriver;
    }

    /**
     * Has Archival Tree
     *
     * @return bool false
     * @access public
     */
    public function hasArchivalTree()
    {
        return false;
    }

    /**
     * Get Archival Tree
     *
     * @param string $context The context from which the call has been made
     * @param string $mode    The mode in which the tree should be generated
     *
     * @return bool false
     * @access public
     */
    public function getArchivalTree($context, $mode)
    {
        return false;
    }

}

?>