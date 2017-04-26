<?php
/**
 * @package     robo-tasks
 * @subpackage
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace redcomponent\robo;

/**
 * Trait loadTasks
 * @package redcomponent\robo
 */
trait loadTasks {
    /**
     * @return SendCodeceptionOutputToSlack
     */
    protected function taskSendCodeceptionOutputToSlack($slackChannel, $slackToken, $codeceptionOutputFolder)
    {
        return new SendCodeceptionOutputToSlack($slackChannel, $slackToken, $codeceptionOutputFolder);
    }

    /**
     * @return WaitForSeleniumStandaloneServer
     */
    protected function taskWaitForSeleniumStandaloneServer()
    {
        return new WaitForSeleniumStandaloneServer();
    }

    /**
     * @return taskCheckRoboFileVersion
     */
    protected function taskCheckRoboFileVersion($clientVersion)
    {
        return new CheckRoboFileVersion($clientVersion);
    }
}

