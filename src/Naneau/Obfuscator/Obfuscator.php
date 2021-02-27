<?php
/**
 * Obfuscator.php
 *
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */

namespace Naneau\Obfuscator;

use Naneau\Obfuscator\Obfuscator\Event\File as FileEvent;
use Naneau\Obfuscator\Obfuscator\Event\FileError as FileErrorEvent;

use PhpParser\NodeTraverserInterface as NodeTraverser;

use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;

use \RegexIterator;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

use \Exception;

/**
 * Obfuscator
 *
 * Obfuscates a directory of files
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */
class Obfuscator
{
    /**
     * the parser
     *
     * @var Parser
     */
    private Parser $parser;

    /**
     * the node traverser
     *
     * @var NodeTraverser
     */
    private NodeTraverser $traverser;

    /**
     * the "pretty" printer
     *
     * @var PrettyPrinter
     */
    private PrettyPrinter $prettyPrinter;

    /**
     * the event dispatcher
     *
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;

    /**
     * The file regex
     *
     * @var string
     **/
    private string $fileRegex = '/\.php$/';

    /**
     * Strip whitespace
     *
     * @param string $directory
     * @param bool $stripWhitespace
     * @param bool $ignoreError
     * @return void
     * @throws Exception
     */
    public function obfuscate(string $directory, bool $stripWhitespace = false, bool $ignoreError = false): void
    {
        foreach ($this->getFiles($directory) as $file) {
            $this->getEventDispatcher()->dispatch(
                new FileEvent($file),
                'obfuscator.file'
            );

            // Write obfuscated source
            file_put_contents($file, $this->obfuscateFileContents($file, $ignoreError));

            // Strip whitespace if required
            if ($stripWhitespace) {
                file_put_contents($file, php_strip_whitespace($file));
            }
        }
    }

    /**
     * Get the file list
     *
     * @param string $directory
     * @return RegexIterator
     */
    private function getFiles(string $directory): RegexIterator
    {
        return new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory)
            ),
            $this->getFileRegex()
        );
    }

    /**
     * Get the regex for file inclusion
     *
     * @return string
     */
    public function getFileRegex(): string
    {
        return $this->fileRegex;
    }

    /**
     * Set the regex for file inclusion
     *
     * @param string $fileRegex
     * @return Obfuscator
     */
    public function setFileRegex(string $fileRegex): Obfuscator
    {
        $this->fileRegex = $fileRegex;

        return $this;
    }

    /**
     * Get the event dispatcher
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }

    /**
     * Set the event dispatcher
     *
     * @param EventDispatcher $eventDispatcher
     * @return Obfuscator
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher): Obfuscator
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Obfuscate a single file's contents
     *
     * @param string $file
     * @param boolean $ignoreError if true, do not throw an Error and
     *                              exit, but continue with next file
     * @return string obfuscated contents
     * @throws Exception
     */
    private function obfuscateFileContents(string $file, bool $ignoreError): string
    {
        try {
            // Input code
            $source = php_strip_whitespace($file);

            // Get AST
            $ast = $this->getTraverser()->traverse(
                $this->getParser()->parse($source)
            );

            return "<?php\n" . $this->getPrettyPrinter()->prettyPrint($ast);
        } catch (Exception $e) {
            if ($ignoreError) {
                $this->getEventDispatcher()->dispatch(
                    new FileErrorEvent($file, $e->getMessage()),
                    'obfuscator.file.error'
                );
            } else {
                throw new RuntimeException(
                    sprintf('Could not parse file "%s"', $file),
                    null,
                    $e
                );
            }
        }
        return '';
    }

    /**
     * Get the node traverser
     *
     * @return NodeTraverser
     */
    public function getTraverser(): NodeTraverser
    {
        return $this->traverser;
    }

    /**
     * Set the node traverser
     *
     * @param NodeTraverser $traverser
     * @return Obfuscator
     */
    public function setTraverser(NodeTraverser $traverser): Obfuscator
    {
        $this->traverser = $traverser;

        return $this;
    }

    /**
     * Get the parser
     *
     * @return Parser
     */
    public function getParser(): Parser
    {
        return $this->parser;
    }

    /**
     * Set the parser
     *
     * @param Parser $parser
     * @return Obfuscator
     */
    public function setParser(Parser $parser): Obfuscator
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Get the "pretty" printer
     *
     * @return PrettyPrinter
     */
    public function getPrettyPrinter(): PrettyPrinter
    {
        return $this->prettyPrinter;
    }

    /**
     * Set the "pretty" printer
     *
     * @param PrettyPrinter $prettyPrinter
     * @return Obfuscator
     */
    public function setPrettyPrinter(PrettyPrinter $prettyPrinter): Obfuscator
    {
        $this->prettyPrinter = $prettyPrinter;

        return $this;
    }
}
