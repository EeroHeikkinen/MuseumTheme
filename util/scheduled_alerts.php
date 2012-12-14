<?php
/**
 * Scheduled alerts sender
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2012.
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
 * @package  Controller
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/developer_manual Wiki
 */

require_once 'util.inc.php';
require_once 'RecordDrivers/Factory.php';  
require_once 'services/MyResearch/lib/Search.php';
require_once 'services/MyResearch/lib/User.php';
require_once 'sys/ConfigArray.php';
require_once 'sys/Interface.php';
require_once 'sys/Mailer.php';
require_once 'sys/SearchObject/Factory.php';
require_once 'sys/Translator.php';
require_once 'sys/VuFindDate.php';

/**
 * Scheduled Alerts Sender
 * 
 * @category VuFind
 * @package  Scheduled_Alerts
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/
 */
class ScheduledAlerts
{
    /**
     * Send scheduled alerts
     *
     * @return void
     */
    public function sendAlerts()
    {
        global $configArray;
        global $interface;
        global $translator;
        
        $iso8601 = 'Y-m-d\TH:i:s\Z';
        
        ini_set('display_errors', true);
        
        $configArray = $mainConfig = readConfig();
        $datasourceConfig = getExtraConfigArray('datasources');

        // Set up time zone. N.B. Don't use msg() or other functions requiring date before this.
        date_default_timezone_set($configArray['Site']['timezone']);

        $this->msg('Sending scheduled alerts');
        
        // Setup Local Database Connection
        ConnectionManager::connectToDatabase();
        
        // Initialize Mailer
        $mailer = new VuFindMailer();
        
        // Find all scheduled alerts
        $sql = 'SELECT * FROM "search" WHERE "schedule" > 0 ORDER BY user_id';
        
        $s =  new SearchEntry();
        $s->query($sql);
        $this->msg('Processing ' . $s->N . ' searches');
        $user = false;
        $interface = false;
        $institution = false;
        $todayTime = new DateTime();
        while ($s->fetch()) {
            $lastTime = new DateTime($s->last_executed);
            if ($s->schedule == 1) {
                // Daily
                if ($todayTime->format('Y-m-d') == $lastTime->format('Y-m-d')) { 
                    $this->msg('Bypassing search ' . $s->id . ': previous execution too recent (daily, ' . $lastTime->format($iso8601) . ')');
                    continue;
                }
            } elseif ($s->schedule == 2) {
                // Weekly
                $diff = $todayTime->diff($lastTime);
                if ($diff->days < 6) {
                    $this->msg('Bypassing search ' . $s->id . ': previous execution too recent (weekly, ' . $lastTime->format($iso8601) . ')');
                    continue;
                }
            } else {
                $this->msg('Search ' . $s->id . ': unknown schedule: ' . $s->schedule);
                continue;
            }
            
            if ($user === false || $s->user_id != $user->id) {
                $user = User::staticGet($s->user_id);
            }
            
            if (!$user->email || trim($user->email) == '') {
                $this->msg('User ' . $user->username . ' does not have an email address, bypassing alert ' . $s->id);
                continue;
            }
            
            $userInstitution = reset(explode('.', $user->username, 2));
            if (!$institution || $institution != $userInstitution) {
                $institution = $userInstitution;
                if (isset($datasourceConfig[$institution]['mainView'])) {
                    // Read institution's configuration
                    $this->msg("Switching to configuration of '$institution'");
                    $configPath = $datasourceConfig[$institution]['mainView'] . '/conf';
                    if (isRelativeFilePath($configPath)) {
                        $configPath = "../$configPath";
                    }
                    $configArray = readConfig($configPath);
                } else {
                    // Use default configuration
                    $this->msg("Switching to default configuration");
                    $configArray = $mainConfig;
                }
                // Start Interface
                $interface = new UInterface();
                $validLanguages = array_keys($configArray['Languages']);
                $dateFormat = new VuFindDate();
            }
            
            $language = $user->language;
            if (!in_array($user->language, $validLanguages)) {
                $language = $configArray['Site']['language'];
            }
        
            $translator = new I18N_Translator(
                array($configArray['Site']['local'] . '/lang', $configArray['Site']['local'] . '/lang_local'),
                $language,
                $configArray['System']['debug']
            );
            $interface->setLanguage($language);
            
            $minSO = unserialize($s->search_object);
            $searchObject = SearchObjectFactory::deminify($minSO);
            $searchTime = time();
            $searchDate = gmdate($iso8601, time());
            $searchObject->setLimit(50);
            $results = $searchObject->processSearch();
            if (PEAR::isError($results)) {
                $this->msg('Search ' . $s->id . ' failed: ' . $results->getMessage());
                continue;
            }
            if ($searchObject->getResultTotal() < 1) {
                $this->msg('No results found for search ' . $s->id);
                continue;
            }
            $newestRecordDate = date($iso8601, strtotime($results['response']['docs'][0]['last_indexed']));
            $lastExecutionDate = $lastTime->format($iso8601);
            if ($newestRecordDate < $lastExecutionDate) { 
                $this->msg('No new results for search ' . $s->id . ": $newestRecordDate < $lastExecutionDate");
            } else {
                $this->msg('New results for search ' . $s->id . ": $newestRecordDate >= $lastExecutionDate");
                
                $interface->assign(
                    'info', 
                    array(
                        'time' =>  $dateFormat->convertToDisplayDate("U", floor($searchObject->getStartTime())),
                        'url'  => $searchObject->renderSearchUrl(),
                        'searchId' => $searchObject->getSearchId(),
                        'description' => $searchObject->displayQuery(),
                        'filters' => $searchObject->getFilterList(),
                        'hits' => $searchObject->getResultTotal(),
                        'speed' => round($searchObject->getQuerySpeed(), 2)."s",
                        'schedule' => $s->schedule,
                        'last_executed' => $s->last_executed
                    )
                );
                $interface->assign('summary', $searchObject->getResultSummary());
                $interface->assign('searchDate', $dateFormat->convertToDisplayDate("U", floor($searchTime)));
                $interface->assign('lastSearchDate', $dateFormat->convertToDisplayDate("U", floor($lastTime->getTimestamp())));
        
                $records = array();
                foreach ($results['response']['docs'] as &$doc) {
                    $record = RecordDriverFactory::initRecordDriver($doc);
                    $records[] = $interface->fetch($record->getSearchResult('email'));
                }
                $interface->assign('recordSet', $records);
        
                $searchObject->close();
                
                // Load template
                $message = $interface->fetch('MyResearch/alert-email.tpl');
                if (strstr($message, 'Warning: Smarty error:')) {
                    $this->msg("Message template processing failed: $message");
                    continue;
                }
                $result = $mailer->send($user->email, $configArray['Site']['email'], translate('Scheduled Alert Results'), $message);
                if (PEAR::isError($result)) {
                    $this->msg("Failed to send message to {$user->email}: " . $result->getMessage());
                    continue;
                }
            }
            
            // Update search date
            $s->changeLastExecuted($searchDate);
        }
        
        $this->msg('Scheduled alerts execution completed');
    }

    /**
     * Output a message with a timestamp
     * 
     * @param string $msg Message
     * 
     * @return void
     */
    protected function msg($msg)
    {
        echo date('Y-m-d H:i:s') . ' [' . getmypid() . "] $msg\n"; 
    }
        
    /**
     * Send email
     * 
     * @param string $sender    Sender's email address
     * @param string $recipient Recipient's email address
     * @param string $subject   Message subject
     * @param string $message   Message body
     * 
     * @return bool Success
     */
    protected function sendEmail($sender, $recipient, $subject, $message)
    {
        mb_internal_encoding('UTF-8');
        
        // Note: According to https://bugs.php.net/bug.php?id=15841 the PHP documentation is wrong, 
        // and CRLF should not be used except on Windows. PHP_EOL should work.
        
        $headers = array(
            'Date' => date('r'),
            'From' => $sender,
            'Mime-Version' => '1.0',
            'Content-Type' => "text/plain; charset=UTF-8; format=flowed",
            'Content-Transfer-Encoding' => '8bit',
            'X-Mailer' => 'VuFind',
        );
        
        $messageBody = $this->getFlowedBody($message) . PHP_EOL;
        
        return mail($this->mimeEncodeAddress($recipient), $this->mimeEncodeHeaderValue($subject), $messageBody, $this->headersToStr($headers), '-f ' . $this->extractAddress($sender));
    }
    
    /**
     * Convert headers to a string
     * 
     * @param array &$headers Message headers
     * 
     * @return string Headers
     */
    protected function headersToStr(&$headers)
    {
        $result = '';
        foreach ($headers as $header => $value) {
            if (!$value) {
                continue;
            }
            if (in_array($header, array("From", "To", "Cc", "Bcc"))) {
                $result .= "$header: " . $this->mimeEncodeAddress($value) . PHP_EOL;
            } else {
                $result .= "$header: $value" . PHP_EOL;
            }
        }
        return $result;
    }
    
    /**
     * Extract email address from string
     * 
     * @param string $address Email address with or without quoted name part
     * 
     * @return string Email address
     */
    protected function extractAddress($address)
    {
        if (preg_match("/<(.+)>/", $address, $matches) == 1) {
            return $matches[1];
        }
        return $address;
    }
    
    /**
     * MIME encode an email address
     * 
     * @param string $address Email address
     * 
     * @return string Encoded address
     */
    protected function mimeEncodeAddress($address)
    {
        if (preg_match("/(.+) (<.+>)/", $address, $matches) == 1) {
            $address = $this->mimeEncodeHeaderValue($matches[1]) . ' ' . $matches[2];
        } elseif (preg_match("/(.+)(<.+>)/", $address, $matches) == 1) {
            $address = $this->mimeEncodeHeaderValue($matches[1]) . $matches[2];
        }
        return $address;
    }
    
    /**
     * MIME encode a header value
     * 
     * @param string $value Header value
     * 
     * @return string Encoded value
     */
    protected function mimeEncodeHeaderValue($value)
    {
        return mb_encode_mimeheader($value, 'UTF-8', 'Q');
    }
}

$alerts = new ScheduledAlerts();
$alerts->sendAlerts();
