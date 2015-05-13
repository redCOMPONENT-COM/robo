<?php
/**
 * @package     robo-tasks
 * @subpackage
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace redcomponent\robo;

use Robo\Task\BaseTask;
use Robo\Common\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;
use Robo\Contract\PrintedInterface;
use Robo\Exception\TaskException;

/**
 * Class CheckRoboFileVersion
 * @package redcomponent\robo
 */
class CheckRoboFileVersion extends BaseTask implements TaskInterface
{
    /**
     * @var string the latest version of RoboFile script
     *
     * @todo: it would be better to retrieve this value by parsing RoboFile.dist.php
     */
    private $version = '1.2';

    /**
     * @var string The actual version at the repo
     */
    private $clientVersion;

    public function __construct($clientVersion)
    {
        $this->clientVersion = $clientVersion;
    }

    /**
     * @return Result
     */
    public function run()
    {
        $this->printTaskInfo('Checking RoboFile Version');

        $this->say("Your RoboFile.php version is $this->clientVersion, the latest version is $this->version");

        if(version_compare($this->version, $this->clientVersion, '>'))
        {
            $this->yell('Your RoboFile.php version is outdated');
            $this->say('Please consider replacing it with the contents of the latest version at:');
            $this->say('https://github.com/redCOMPONENT-COM/robo/blob/master/src/RoboFile.dist.php');

            return new \Robo\Result($this, 0, 'Please update your RoboFile');
        }

        return new \Robo\Result($this, 0, 'Your RoboFile is up to date');
    }

}
