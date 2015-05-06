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
use Robo\Common\Timer;

/**
 * Class HelloWorldTask
 * @package redcomponent\robo
 */
class HelloWorld extends BaseTask implements TaskInterface
{
    use Timer;

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();
        $this->printTaskInfo('saying Hello World');
        $this->stopTimer();

        return new Result(
            $this,
            0,
            'Hello World',
            ['time' => $this->getExecutionTime()]
        );
    }

}
