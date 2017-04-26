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
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\ProgressBar;


/**
 * Class WaitForSeleniumStandaloneServerTask
 * @package redcomponent\robo
 */
class WaitForSeleniumStandaloneServer extends BaseTask implements TaskInterface
{

    use Timer;

    /**
     * @var the domain and port to selenium hub site
     */
    private $url;

    public function __construct($url = null)
    {
        if (is_null($url)) {
            $this->url = 'http://localhost:4444/wd/hub';
        }
    }

    /**
     * @return Result
     */
    public function run()
    {
        $this->startTimer();

        $this->printTaskInfo('Waiting for Selenium Standalone server to launch');

        $timeout = 0;
        while(!$this->isUrlAvailable($this->url))
        {
        	if (method_exists($this, 'getOutput'))
	        {
		        $this->getOutput()->write('.');
	        }
            elseif (method_exists($this, 'getOutputData'))
            {
	            $this->getOutputData()->write('.');
            }

            // If selenium has not started after 60 seconds then die
            if ($timeout > 60)
            {
                $error = new Result(
                    $this,
                    1,
                    'Selenium server was not launched',
                    []
                );

                $error::$stopOnFail = true;

                return $error;
            }

            sleep(1);
            $timeout++;
        }

        $this->stopTimer();

        return new Result(
            $this,
            0,
            'Selenium server is ready',
            ['time' => $this->getExecutionTime()]
        );
    }

    private function isUrlAvailable($url)
    {
        try {
            $command = "curl  --retry 6 --retry-delay 10 --output /dev/null --silent $this->url";
            $process = new Process($command);
            $process->setTimeout(null);
            $process->run();

            // to debug: $this->say('The exit code is: ' . $process->getExitCode());

            if (0 == $process->getExitCode()) {
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            $this->say('selenium not yet ready');

            return false;
        }
        return true;
    }
}
