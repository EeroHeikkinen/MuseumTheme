<?php
/**
 * Wikipedia Entries
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2013.
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
 * @package  Recommendations
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */

require_once 'sys/RSSUtils.php';
require_once 'sys/Recommend/Interface.php';

/**
 * Wikipedia Entries
 *
 * This class provides Wikipedia entries in search results
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */

class Wikipedia implements RecommendationInterface
{
    protected $lookfor = '';
    protected $searchType = '';
    
    /**
     * Constructor
     *
     * Establishes base settings for making recommendations.
     *
     * @param object $searchObject The SearchObject requesting recommendations.
     * @param string $params       Additional settings from searches.ini.
     *
     * @access public
     */
    public function __construct($searchObject, $params)
    {
        // Get search params 
        $this->lookfor = isset($_REQUEST['lookfor'])
            ? $_REQUEST['lookfor'] : '';
        if (empty($this->lookfor)) {
            if (!is_object($searchObject)) {
                return;
            }
            $this->lookfor = $searchObject->extractAdvancedTerms();
        }
        
        $this->searchType = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
    }

    /**
     * init
     *
     * Called before the SearchObject performs its main search.  This may be used
     * to set SearchObject parameters in order to generate recommendations as part
     * of the search.
     *
     * @return void
     * @access public
     */
    public function init()
    {
        // No action needed here.
    }

    /**
     * process
     *
     * Called after the SearchObject has performed its main search.  This may be
     * used to extract necessary information from the SearchObject or to perform
     * completely unrelated processing.
     *
     * @return void
     * @access public
     */
    public function process()
    {
        global $interface;

        if (empty($this->lookfor)) {
            return;
        }
        $lookfor = $this->lookfor;
        if ($this->searchType == 'Author') {
            // Clean up author name
            $lookfor = $this->cleanupAuthorName($lookfor);
        }
        
        // Only use first two characters of language string; Wikipedia
        // uses language domains but doesn't break them up into regional
        // variations like pt-br or en-gb.
        $wiki_lang = substr($interface->getLanguage(), 0, 2);
        $info = $this->getWikipedia($lookfor, $wiki_lang);
        $interface->assign('wiki_lang', $wiki_lang);
        if (!PEAR::isError($info)) {
            $interface->assign('info', $info);
        }
        
    }

    /**
     * Clean up the author search term for Wikipedia search 
     * 
     * @param string $authorName Author name
     *  
     * @return string Clean author name
     */
    protected function cleanupAuthorName($authorName)
    {
        // Clean up author string
        $author = str_replace('"', '', $authorName);
        if (substr($author, strlen($author) - 1, 1) == ",") {
            $author = substr($author, 0, strlen($author) - 1);
        }
        $author = explode(',', $author);

        // Create First Name
        $fname = '';
        if (isset($author[1])) {
            $fname = $author[1];
            if (isset($author[2])) {
                // Remove punctuation
                if ((strlen($author[2]) > 2)
                    && (substr($author[2], -1) == '.')
                ) {
                    $author[2] = substr($author[2], 0, -1);
                }
                $fname = $author[2] . ' ' . $fname;
            }
        }

        // Remove dates
        $fname = preg_replace('/[0-9]+-[0-9]*/', '', $fname);

        // Build Author name to display.
        if (substr($fname, -3, 1) == ' ') {
            // Keep period after initial
            $authorName = $fname . ' ';
        } else {
            // No initial so strip any punctuation from the end
            if ((substr(trim($fname), -1) == ',')
                || (substr(trim($fname), -1) == '.')
            ) {
                $authorName = substr(trim($fname), 0, -1) . ' ';
            } else {
                $authorName = $fname . ' ';
            }
        }
        $authorName .= $author[0];
        
        return $authorName;
    }
    
    /**
     * getWikipedia
     *
     * This method is responsible for connecting to Wikipedia via the REST API
     * and pulling the content for the relevant author.
     *
     * @param string $author The author name to search for
     * @param string $lang   The language code of the language to use
     *
     * @return mixed         Info array or PEAR_Error object.
     * @author Andrew Nagy <vufind-tech@lists.sourceforge.net>
     * @author Ere Maijala <ere.maijala@helsinki.fi>
     */
    protected function getWikipedia($author, $lang = null)
    {
        if ($lang) {
            $this->_lang = $lang;
        }

        $url = "http://$this->_lang.wikipedia.org/w/api.php" .
               '?action=query&prop=revisions&rvprop=content&format=php' .
               '&list=allpages&titles=' . urlencode($author);
        
        $client = new Proxy_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);

        $result = $client->sendRequest();
        if (PEAR::isError($result)) {
            return $result;
        }

        $info = $this->parseWikipedia(unserialize($client->getResponseBody()));
        if (!PEAR::isError($info)) {
            return $info;
        }
    }

    /**
     * This method is responsible for obtaining an image URL based on a name.
     *
     * @param string $imageName The image name to look up
     *
     * @return mixed            URL on success, false on failure
     */
    protected function getWikipediaImageURL($imageName)
    {
        $url = "http://$this->_lang.wikipedia.org/w/api.php" .
               '?prop=imageinfo&action=query&iiprop=url&iiurlwidth=150&format=php' .
               '&titles=Image:' . $imageName;

        $client = new Proxy_Request();
        $client->setMethod(HTTP_REQUEST_METHOD_GET);
        $client->setURL($url);
        $result = $client->sendRequest();
        if (PEAR::isError($result)) {
            return false;
        }

        if ($response = $client->getResponseBody()) {
            if ($imageinfo = unserialize($response)) {
                if (isset($imageinfo['query']['pages']['-1']['imageinfo'][0]['url'])) {
                    $imageUrl
                        = $imageinfo['query']['pages']['-1']['imageinfo'][0]['url'];
                }

                // Hack for wikipedia api, just in case we couldn't find it
                //   above look for a http url inside the response.
                if (!isset($imageUrl)) {
                    preg_match('/\"http:\/\/(.*)\"/', $response, $matches);
                    if (isset($matches[1])) {
                        $imageUrl = 'http://' .
                            substr($matches[1], 0, strpos($matches[1], '"'));
                    }
                }
            }
        }

        return isset($imageUrl) ? $imageUrl : false;
    }

    /**
     * This method is responsible for parsing the output from the Wikipedia
     * REST API.
     *
     * @param string $body The Wikipedia response to parse
     *
     * @return array
     * @author Rushikesh Katikar <rushikesh.katikar@gmail.com>
     * @author Ere Maijala <ere.maijala@helsinki.fi>
     */
    protected function parseWikipedia($body)
    {
        global $configArray;

        // Check if data exists or not
        if (isset($body['query']['pages']['-1'])) {
            return new PEAR_Error('No page found');
        }

        // Get the default page
        $body = array_shift($body['query']['pages']);
        $info['name'] = $body['title'];

        // Get the latest revision
        $body = array_shift($body['revisions']);
        // Check for redirection
        $as_lines = explode("\n", $body['*']);
        if (stristr($as_lines[0], '#REDIRECT')) {
            preg_match('/\[\[(.*)\]\]/', $as_lines[0], $matches);
            return $this->getWikipedia($matches[1]);
        }

        /* Infobox */

        // We are looking for the infobox inside "{{...}}"
        //   It may contain nested blocks too, thus the recursion
        preg_match_all('/\{([^{}]++|(?R))*\}/s', $body['*'], $matches);
        // print "<p>".htmlentities($body['*'])."</p>\n";
        $infoboxStr = '';
        foreach ($matches[1] as $m) {
            // If this is the Infobox
            if (substr($m, 0, 8) == "{Infobox") {
                // Keep the string for later, we need the body block that follows it
                $infoboxStr = "{".$m."}";
                break;
            }
        }
        
        // If Infobox not found with name, try to find it by content
        if (!$infoboxStr && isset($matches[1]) && $matches[1]) {
            $index = 0;
            foreach ($matches[1] as $m) {
                if (strstr($m, "\n|") || strstr($m, "\n |")) {
                    $infoboxStr = "{".$m."}";
                    break;
                }
                if (++$index > 2) {
                    break;
                }
            }
        }

        if ($infoboxStr) {
            $infobox = explode("\n", substr($infoboxStr, 2, -2));             
            // Look through every row of the infobox
            foreach ($infobox as $row) {
                $row = preg_replace('/^[\s\|]*/', '', $row);
                $data  = explode("=", $row);
                $key   = trim(array_shift($data));
                $value = trim(join("=", $data));
    
                // At the moment we only want stuff related to the image.
                switch (strtolower($key)) {
                case "img":
                case "image":
                case "image:":
                case "image_name":
                case "kuva":
                case "kuvan_nimi":
                case "bild":
                    $imageName = str_replace(' ', '_', $value);
                    break;
                case "caption":
                case "img_capt":
                case "image_caption":
                case "kuvateksti":
                case "bildtext":
                    $image_caption = $value;
                    break;
                default:
                    /* Nothing else... yet */
                    break;
                }
            }
        }
        
        /* Image */

        // If we didn't successfully extract an image from the infobox, let's see if
        // we can find one in the body -- we'll just take the first match:
        if (!isset($imageName)) {
            $pattern = '/(\x5b\x5b)(Image|Kuva|Bild):([^\x5d]*)(\x5d\x5d)/U';
            preg_match_all($pattern, $body['*'], $matches);
            if (isset($matches[3][0])) {
                $parts = explode('|', $matches[3][0]);
                $imageName = str_replace(' ', '_', $parts[0]);
                if (count($parts) > 1) {
                    $image_caption = strip_tags(
                        preg_replace('/({{).*(}})/U', '', $parts[count($parts) - 1])
                    );
                }
            }
        }

        // Given an image name found above, look up the associated URL:
        if (isset($imageName)) {
            $imageUrl = $this->getWikipediaImageURL($imageName);
        }

        /* Body */

        if ($infoboxStr) {
            // Start of the infobox
            $start  = strpos($body['*'], $infoboxStr);
            // + the length of the infobox
            $offset = strlen($infoboxStr);
            // Every after the infobox
            $body   = substr($body['*'], $start + $offset);
        } else {
            // No infobox -- use whole thing:
            $body = $body['*'];
        }
            
        // Find the first heading
        $end    = strpos($body, "==");
        // Now cull our content back to everything before the first heading
        $body   = trim(substr($body, 0, $end));

        // Remove unwanted image/file links
        // Nested brackets make this annoying: We can't add 'File' or 'Image' as
        //    mandatory because the recursion fails, or as optional because then
        //    normal links get hit.
        //    ... unless there's a better pattern? TODO
        // eg. [[File:Johann Sebastian Bach.jpg|thumb|Bach in a 1748 portrait by
        //     [[Elias Gottlob Haussmann|Haussmann]]]]
        $open    = "\\[";
        $close   = "\\]";
        $content = "(?>[^\\[\\]]+)";  // Anything but [ or ]
        // We can either find content or recursive brackets:
        $recursive_match = "($content|(?R))*";
        preg_match_all("/".$open.$recursive_match.$close."/Us", $body, $new_matches);
        // Loop through every match (link) we found
        if (is_array($new_matches)) {
            foreach ($new_matches as $nm) {
                // Might be an array of arrays
                if (is_array($nm)) {
                    foreach ($nm as $n) {
                        // If it's a file link get rid of it
                        if (strtolower(substr($n, 0, 7)) == "[[file:"
                            || strtolower(substr($n, 0, 8)) == "[[image:"
                            || preg_match('/^\[\[\w{1,8}:/', $n)
                        ) {
                            $body = str_replace($n, "", $body);
                        }
                    }
                } else {
                    // Or just a normal array...
                    // If it's a file link get rid of it
                    if (strtolower(substr($n, 0, 7)) == "[[file:"
                        || strtolower(substr($n, 0, 8)) == "[[image:"
                        || preg_match('/^\[\[\w{1,5}:/', $n)
                    ) {
                        $body = str_replace($nm, "", $body);
                    }
                }
            }
        }

        // Initialize arrays of processing instructions
        $pattern = array();
        $replacement = array();

        // Convert wikipedia links
        $pattern[] = '/(\x5b\x5b)([^\x5d|]*)(\x5d\x5d)/Us';
        $replacement[] = '<a href="' . $configArray['Site']['url'] .
            '/Search/Results?lookfor=%22$2%22&amp;type=AllFields">$2</a>';
        //$pattern[] = '/(\x5b\x5b)([^\x5d|]*)(\x5d\x5d)/Us';
        //$replacement[] = '';
        $pattern[] = '/(\x5b\x5b)([^\x5d]*)\x7c([^\x5d]*)(\x5d\x5d)/Us';
        $replacement[] = '<a href="' . $configArray['Site']['url'] .
            '/Search/Results?lookfor=%22$2%22&amp;type=AllFields">$3</a>';
        //$pattern[] = '/(\x5b\x5b)([^\x5d]*)\x7c([^\x5d]*)(\x5d\x5d)/Us';
        //$replacement[] = '';
        
        // Fix pronunciation guides
        $pattern[] = '/({{)pron-en\|([^}]*)(}})/Us';
        $replacement[] = translate("pronounced") . " /$2/";

        // Fix dashes
        $pattern[] = '/{{ndash}}/';
        $replacement[] = ' - ';

        // Removes citations
        $pattern[] = '/({{)[^}]*(}})[;,] /Us';
        $replacement[] = "";
        $pattern[] = '/({{)[^}]*(}})/Us';
        $replacement[] = "";
        //  <ref ... > ... </ref> OR <ref> ... </ref>
        $pattern[] = '/<ref[^\/]*>.*<\/ref>/Us';
        $replacement[] = "";
        //    <ref ... />
        $pattern[] = '/<ref.*\/>/Us';
        $replacement[] = "";

        // Removes comments followed by carriage returns to avoid excess whitespace
        $pattern[] = '/<!--.*-->\n*/Us';
        $replacement[] = '';

        // Formatting
        $pattern[] = "/'''([^']*)'''/Us";
        $replacement[] = '<strong>$1</strong>';

        // Trim leading newlines (which can result from leftovers after stripping
        // other items above).  We want this to be greedy.
        $pattern[] = '/^\n*/s';
        $replacement[] = '';

        // Convert multiple newlines into two breaks
        // We DO want this to be greedy
        $pattern[] = "/\n{2,}/s";
        $replacement[] = '<br/><br/>';

        $body = preg_replace($pattern, $replacement, $body);

        if (isset($imageUrl) && $imageUrl != false) {
            $info['image'] = $imageUrl;
            if (isset($image_caption)) {
                $info['altimage'] = preg_replace($pattern, $replacement, $image_caption);
            }
        }
        $info['description'] = $body;

        return $info;
    }

    /**
     * getTemplate
     *
     * This method provides a template name so that recommendations can be displayed
     * to the end user.  It is the responsibility of the process() method to
     * populate all necessary template variables.
     *
     * @return string The template to use to display the recommendations.
     * @access public
     */
    public function getTemplate()
    {
        return 'Search/Recommend/Wikipedia.tpl';
    }
}

?>
