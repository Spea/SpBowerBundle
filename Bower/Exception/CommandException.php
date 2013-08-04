<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower\Exception;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class CommandException extends RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $commandError;

    /**
     * @param string $commandName
     * @param string $commandError
     */
    public function __construct($commandName, $commandError)
    {
        $message = "Something went wrong while executing the command %s\n %s";
        $message = sprintf($message, $commandName, $commandError);
        parent::__construct($message);
        $this->commandError = $commandError;
    }

    /**
     * @return string
     */
    public function getCommandError()
    {
        return $this->commandError;
    }
}
