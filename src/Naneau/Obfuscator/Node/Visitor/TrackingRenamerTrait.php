<?php
/**
 * SkipTrait.php
 *
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Identifier;

/**
 * SkipTrait
 *
 * Renaming trait, for renaming things that require tracking
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
trait TrackingRenamerTrait
{
    /**
     * Renamed variables
     *
     * @var string[]
     **/
    private array $renamed = [];

    /**
     * Record renaming of method
     *
     * @param string $method
     * @param string $newName
     * @return self
     */
    protected function renamed(string $method, string $newName): self
    {
        $this->renamed[$method] = $newName;

        return $this;
    }

    /**
     * Get new name of a method
     *
     * @param string $method
     * @return string
     */
    protected function getNewName(string $method): string
    {
        if (!$this->isRenamed($method)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" was not renamed',
                $method
            ));
        }

        return $this->renamed[$method];
    }

    /**
     * Has a method been renamed?
     *
     * @param string|Node $method
     * @return bool
     */
    protected function isRenamed($method): bool
    {
        if (empty($method)) {
            return false;
        }

        // Ignore variable functions
        if (!is_string($method) && !($method instanceof Identifier)) {
            return false;
        }

        return isset($this->renamed[(string)$method]);
    }

    /**
     * Reset renamed list
     *
     * @return self
     **/
    protected function resetRenamed(): self
    {
        $this->renamed = [];

        return $this;
    }
}
