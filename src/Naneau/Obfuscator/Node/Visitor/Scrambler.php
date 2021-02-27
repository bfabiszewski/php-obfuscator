<?php
/**
 * Scrambler.php
 *
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\StringScrambler;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

use \InvalidArgumentException;

/**
 * Scrambler
 *
 * Base class for scrambling visitors
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
abstract class Scrambler extends NodeVisitorAbstract
{
    /**
     * The string scrambler
     *
     * @var StringScrambler
     **/
    private StringScrambler $scrambler;

    /**
     * Variables to ignore
     *
     * @var string[]
     **/
    private array $ignore = [];

    /**
     * Constructor
     *
     * @param StringScrambler $scrambler
     * @return void
     **/
    public function __construct(StringScrambler $scrambler)
    {
        $this->setScrambler($scrambler);
    }

    /**
     * Add a variable name to ignore
     *
     * @param string|string[] $ignore
     * @return Scrambler
     **/
    public function addIgnore($ignore): Scrambler
    {
        if (is_string($ignore)) {
            $this->ignore = array_merge($this->ignore, array($ignore));
        } else if (is_array($ignore)) {
            $this->ignore = array_merge($this->ignore, $ignore);
        } else {
            throw new InvalidArgumentException('Invalid ignore type passed');
        }
        return $this;
    }

    /**
     * Scramble a property of a node
     *
     * @param Node $node
     * @return Node
     **/
    protected function scramble(Node $node): ?Node
    {
        // String/value to scramble
        if ($node->name instanceof Node\Identifier) {
            $toScramble = $node->name->toString();
        } else if (is_string($node->name)) {
            $toScramble = $node->name;
        } else {
            // We ignore to scramble if it's not string (ex: a variable variable name)
            return null;
        }

        // Make sure there's something to scramble
        if ($toScramble === '') {
            throw new InvalidArgumentException('Value empty for node, can not scramble');
        }

        // Should we ignore it?
        if (in_array($toScramble, $this->getIgnore())) {
            return $node;
        }

        $scrambled = $this->scrambleString($toScramble);

        if ($node->name instanceof Node\Identifier) {
            $node->name->name = $scrambled;
        } else {
            $node->name = $scrambled;
        }

        // Return the node
        return $node;
    }

    /**
     * Get variables to ignore
     *
     * @return string[]
     */
    public function getIgnore(): array
    {
        return $this->ignore;
    }

    /**
     * Set variables to ignore
     *
     * @param string[] $ignore
     * @return Scrambler
     */
    public function setIgnore(array $ignore): Scrambler
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * Scramble a string
     *
     * @param string $string
     * @return string
     */
    protected function scrambleString(string $string): string
    {
        return 's' . $this->getScrambler()->scramble($string);
    }

    /**
     * Get the string scrambler
     *
     * @return StringScrambler
     */
    public function getScrambler(): StringScrambler
    {
        return $this->scrambler;
    }

    /**
     * Set the string scrambler
     *
     * @param StringScrambler $scrambler
     * @return Scrambler
     */
    public function setScrambler(StringScrambler $scrambler): Scrambler
    {
        $this->scrambler = $scrambler;

        return $this;
    }
}
