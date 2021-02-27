<?php
/**
 * The file that handles parsing error events
 *
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */

namespace Naneau\Obfuscator\Obfuscator\Event;


/**
 * FileError
 *
 * The file being obfuscated that causes an error
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */
class FileError extends File
{
    /**
     * The error message from Exception
     * @var string
     **/
    private string $errorMessage;

    /**
     * Constructor
     *
     * @param string $file
     * @param $errorMessage
     */
    public function __construct(string $file, string $errorMessage)
    {
        parent::__construct($file);
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
