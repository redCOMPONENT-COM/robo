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
     * @return HelloWorld
     */
    protected function taskHelloWorld()
    {
        return new HelloWorld();
    }
}

