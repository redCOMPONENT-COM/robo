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
 * Class SendCodeceptionOutputToSlack
 * @package redcomponent\robo
 */
class SendCodeceptionOutputToSlack extends BaseTask implements TaskInterface
{
    use Timer;

    /**
     * @var string ID of the Slack channel where you want to send the message
     */
    private $slackChannel;

    /**
     * @var string Slack token athorised token
     *
     * @see https://api.slack.com/tokens
     */
    private $slackToken;

    /**
     * @var string Path to codeception output folder, by default tests/_output
     */
    private $codeceptionOutputFolder;

    /**
     * @var string
     */
    protected $command;


    public function __construct($slackChannel, $slackToken = null, $codeceptionOutputFolder = null)
    {
        if (is_null($codeceptionOutputFolder)) {
            $this->codeceptionOutputFolder = 'tests/_output';
        }
        else {
            $this->codeceptionOutputFolder = $codeceptionOutputFolder;
        }

        $this->slackChannel = $slackChannel;
        $this->slackToken = $slackToken;
    }

    /**
     * @return Result
     */
    public function run()
    {
        if (!$this->slackToken)
        {
            $result = new Result(
                $this,
                1,
                'Slack security token was not received',
                ['time' => $this->getExecutionTime()]
            );

            return $result;
        }

        $this->printTaskInfo('Check if there is Codeception snapshots and sending them to Slack.');
        $this->printTaskInfo('Looking for snapshots at:' . $this->codeceptionOutputFolder);


        if (!file_exists($this->codeceptionOutputFolder) || !(new \FilesystemIterator($this->codeceptionOutputFolder))->valid())
        {
            $this->printTaskInfo('There were no errors found by Codeception');

            return new Result(
                $this,
                0,
                'No errors were found by Codeception',
                []
            );
        }

        $error = false;
        $errorSnapshot = '';

        $this->startTimer();

        // Loop throught Codeception snapshots
        if ($handler = opendir($this->codeceptionOutputFolder))
        {

            while (false !== ($errorSnapshot = readdir($handler)))
            {
                // Avoid sending system files or html files
                if ('.' === substr($errorSnapshot, 0, 1) || 'html' == substr($errorSnapshot, -4))
                {
                    continue;
                }

                $initial_comment = 'initial_comment="error found"';

                if(getenv(TRAVIS))
                {
                    $travisLogUrl = 'https://magnum.travis-ci.com/';
                    if (getenv(SLACK_ENCRYPTED_TOKEN))
                    {
                        // Means that we are in a public repository
                        $travisLogUrl = 'https://travis-ci.org/';
                    }

                    $initial_comment = 'initial_comment="error found by travis in' .
                        getenv(TRAVIS_REPO_SLUG)
                        . 'at test: '
                        . substr($errorSnapshot,0,-9)
                        . ' on build: ' . $travisLogUrl . getenv(TRAVIS_REPO_SLUG) . '/builds/"'
                        . getenv('TRAVIS_JOB_ID') . ' -F';

                }


                // Sends error snapshot to Slack channel
                $command = 'curl -F file=@' . $this->codeceptionOutputFolder . '/' . $errorSnapshot . ' -F '
                    . 'channels='. $this->slackChannel  . ' -F '
                    . 'title=Codeception_error -F '
                    . $initial_comment
                    . 'token=' . $this->slackToken . ' '
                    . 'https://slack.com/api/files.upload';

                $response = json_decode(shell_exec($command));

                $result = '';

                if($response->ok) {
                    $error = false;
                }
                else {
                    $error = true;
                }
            }
        }

        closedir($handler);

        $this->stopTimer();

        if($error) {
            $result = new Result(
                $this,
                1,
                'Slack could not be reached',
                ['time' => $this->getExecutionTime()]
            );
        }
        else
        {
            $result = new Result(
                $this,
                0,
                'Error images have been sent to Slack',
                ['time' => $this->getExecutionTime()]
            );
        }

        return $result;
    }

}
