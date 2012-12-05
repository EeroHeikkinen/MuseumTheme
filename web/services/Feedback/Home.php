<?php
/**
 * Home action for Feedback module
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
 * @package  Controller_Feedback
 * @author   Kalle Pyykkönen <kalle.pyykkonen@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Action.php';

/**
 * Home action for Feedback module
 *
 * @category VuFind
 * @package  Controller_Feedback
 * @author   Kalle Pyykkönen <kalle.pyykkonen@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Home extends Action
{
    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $configArray;
        $submitted = false;
        
        if (isset($_POST['submit'])) {
            $interface->assign('from', $_POST['email']);
            $interface->assign('name', $_POST['name']);
            $interface->assign('category', $_POST['category']);
            $interface->assign('feedback_url', $_POST['feedback_url']);
            $interface->assign('message', $_POST['message']);
            if ($_POST['question'] == translate("feedback_captcha_answer")) {
                $result = $this->sendEmail();
                if (!PEAR::isError($result)) {
                    $submitted = true;
                } else {
                    $interface->assign('errorMsg', $result->getMessage());
                }
            } else {
                $interface->assign('captchaError', true);
                $interface->assign('errorMsg', translate("feedback_captcha_error"));
            }
        }
        $interface->assign('submitted', $submitted);
        $interface->setPageTitle(translate("Feedback"));
        $interface->setTemplate('feedback.tpl');
        $interface->display('layout.tpl');
    }
    
    
    /**
     * Send feedback email.
     *
     * @return mixed        Boolean true on success, PEAR_Error on failure.
     * @access public
     */
    protected function sendEmail()
    {
        global $interface, $configArray;
        $to = $configArray['Site']['email'];
        $subject = translate('Feedback') . ': ' . translate($_POST['category']);
        $name = empty($_POST['name']) ? translate('Anonymous') : $_POST['name'];
        $email = empty($_POST['email']) ? $configArray['Site']['email'] : $_POST['email'];
        $from = $email;
        $body = $interface->fetch('Emails/general-feedback.tpl');
        $mail = new VuFindMailer();
        return $mail->send($to, $from, $subject, $body);
    }    
}

?>
