<?php
/**
 * File.php
 *
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */

namespace Naneau\Obfuscator\Obfuscator\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * File
 *
 * A file is being obfuscated
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */
class File extends GenericEvent
{
    /**
     * The file
     *
     * @var string
     **/
    private string $file;

    /**
     * Constructor
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct();
        $this->setFile($file);
    }

    /**
     * Get the file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Set the file
     *
     * @param string $file
     * @return File
     */
    public function setFile(string $file): File
    {
        $this->file = $file;

        return $this;
    }
}
