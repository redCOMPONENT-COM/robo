<?php
/**
 * @package     robo-tasks
 * @subpackage
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace redcomponent\robo;

use Robo\Result;
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
     */
    private $version = '1.0';

    /**
     * @var string The actual version at the repo
     */
    private $clientVersion = '1.0';

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

        if(version_compare($this->version, $this->clientVersion, '>'))
        {
            $this->say('your RoboFile version is outdated, please replace it with the following content:');
            $this->say('------------------------------ content start -----------------------------------');
            $this->say(file_get_contents('RoboFile.dist.php'));
            $this->say('------------------------------ content end -------------------------------------');

            return new Result(
                $this,
                1,
                'Please update your RoboFile',
                []
            );
        }


        return new Result(
            $this,
            0,
            'Your RoboFile is up to date',
            ['time' => $this->getExecutionTime()]
        );
    }

}
