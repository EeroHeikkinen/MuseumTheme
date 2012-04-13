<?php
/**
 * Create all the hierarchy files which are used for looking up hierarchichal trees.
 * This script will search the solr index and create the files needed so they don't 
 * need to be built at runtime. if this script is run after every index, the caching
 * time for hierarchy trees can be set to -1 so that trees are always assumed to be
 * up to date.
 * 
 * -!!!!-This script is specifically for trees built for JSTree.-!!!!-
 *
 * PHP version 5
 *
 * Copyright (C) National Library of Ireland 2012.
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
 * @package  Utilities
 * @author   Lutz Biedinger <lutz.biedinger@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki Wiki
 */

require_once 'util.inc.php';  
require_once 'sys/ConfigArray.php';
require_once 'sys/solr.php';
require_once 'sys/SearchObject/solr.php';
require_once 'sys/SearchObject/Factory.php';
require_once 'RecordDrivers/Factory.php';
require_once 'sys/hierarchy/HierarchyTreeGenerator_JSTree.php';

print "getting config\r\n";
$configArray = readConfig();

print "creating Search Object\r\n";
$solrSearchObject = new SearchObject_Solr();
if (!$solrSearchObject) {
    die("Error: No connection to solr index\n");
}

print "Getting Full Field Facets\r\n";
$hierarchyTopFacets = $solrSearchObject->getFullFieldFacets(array("hierarchy_top_id"));

$db = ConnectionManager::connectToIndex();

print "Iterate through Values\r\n";
foreach ($hierarchyTopFacets["hierarchy_top_id"]["data"] as $hierarchyTopFacet){
	$topRecord = $db->getRecord($hierarchyTopFacet[0]);
	$RecDriver = RecordDriverFactory::initRecordDriver($topRecord);
	if ($RecDriver->getHierarchyType()){
		//only do this if the record is actually a hierarchy type record
		$generator = new HierarchyTreeGenerator_JSTree($RecDriver);
		$generator->generateXMLfromSolr($hierarchyTopFacet[0]);
	}
}

?>