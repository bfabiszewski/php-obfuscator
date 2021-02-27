<?php
/**
 * ScramblePrivateProperty.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\Node\Visitor\Scrambler as ScramblerVisitor;

use PhpParser\Node;

use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property;

use PhpParser\Node\Expr\PropertyFetch;

/**
 * ScramblePrivateProperty
 *
 * Renames private properties
 *
 * WARNING
 *
 * See warning for private method scrambler
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScramblePrivateProperty extends ScramblerVisitor
{
    use TrackingRenamerTrait;
    use SkipTrait;

    /**
     * Before node traversal
     *
     * @param Node[] $nodes
     * @return Node[]
     **/
    public function beforeTraverse(array $nodes): array
    {
        $this
            ->resetRenamed()
            ->scanPropertyDefinitions($nodes);

        return $nodes;
    }

    /**
     * Recursively scan for private method definitions and rename them
     *
     * @param Node[] $nodes
     * @return void
     **/
    private function scanPropertyDefinitions(array $nodes): void
    {
        foreach ($nodes as $node) {
            // Scramble the private method definitions
            if ($node instanceof Property && ($node->flags & ClassNode::MODIFIER_PRIVATE)) {
                foreach ($node->props as $property) {

                    // Record original name and scramble it
                    $originalName = $property->name->toString();
                    $this->scramble($property);

                    // Record renaming
                    $this->renamed($originalName, $property->name);
                }

            }

            // Recurse over child nodes
            if (isset($node->stmts) && is_array($node->stmts)) {
                $this->scanPropertyDefinitions($node->stmts);
            }
        }
    }

    /**
     * Check all variable nodes
     *
     * @param Node $node
     * @return Node|null
     */
    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof PropertyFetch) {

            if (!(is_string($node->name) || $node->name instanceof Node\Identifier) || $node->var->name !== "this") {
                return null;
            }

            if ($this->isRenamed($node->name)) {
                $node->name = $this->getNewName($node->name);
                return $node;
            }
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch) {
            if ($node->class->toString() !== "self") {
                return null;
            }

            if ($this->isRenamed($node->name)) {
                $node->name = $this->getNewName($node->name);
                return $node;
            }
        }

        return null;
    }
}
