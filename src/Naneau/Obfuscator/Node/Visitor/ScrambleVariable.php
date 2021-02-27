<?php
/**
 * ScrambleVariable.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\Node\Visitor\Scrambler as ScramblerVisitor;
use Naneau\Obfuscator\StringScrambler;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;

/**
 * ScrambleVariable
 *
 * Renames parameters
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScrambleVariable extends ScramblerVisitor
{
    /**
     * Constructor
     *
     * @param StringScrambler $scrambler
     * @return void
     **/
    public function __construct(StringScrambler $scrambler)
    {
        parent::__construct($scrambler);

        $this->setIgnore(array(
            'this', '_SERVER', '_POST', '_GET', '_REQUEST', '_COOKIE',
            '_SESSION', '_ENV', '_FILES'
        ));
    }

    /**
     * Check all variable nodes
     *
     * @param Node $node
     * @return Node|null
     **/
    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof Variable) {
            return $this->scramble($node);
        }

        return null;
    }
}
