<?php

declare (strict_types=1);
namespace Rector\PostRector\Rector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use Rector\CodingStyle\Application\UseImportsRemover;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\Configuration\RenamedClassesDataCollector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Renaming\Collector\RenamedNameCollector;
use Rector\Renaming\NodeManipulator\ClassRenamer;
final class ClassRenamingPostRector extends \Rector\PostRector\Rector\AbstractPostRector
{
    /**
     * @readonly
     */
    private ClassRenamer $classRenamer;
    /**
     * @readonly
     */
    private RenamedClassesDataCollector $renamedClassesDataCollector;
    /**
     * @readonly
     */
    private UseImportsRemover $useImportsRemover;
    /**
     * @readonly
     */
    private RenamedNameCollector $renamedNameCollector;
    /**
     * @var \Rector\PhpParser\Node\CustomNode\FileWithoutNamespace|\PhpParser\Node\Stmt\Namespace_|null
     */
    private $rootNode = null;
    /**
     * @var array<string, string>
     */
    private array $oldToNewClasses = [];
    public function __construct(ClassRenamer $classRenamer, RenamedClassesDataCollector $renamedClassesDataCollector, UseImportsRemover $useImportsRemover, RenamedNameCollector $renamedNameCollector)
    {
        $this->classRenamer = $classRenamer;
        $this->renamedClassesDataCollector = $renamedClassesDataCollector;
        $this->useImportsRemover = $useImportsRemover;
        $this->renamedNameCollector = $renamedNameCollector;
    }
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : array
    {
        // ensure reset early on every run to avoid reuse existing value
        $this->rootNode = $this->resolveRootNode($nodes);
        return $nodes;
    }
    public function enterNode(Node $node) : ?Node
    {
        // no longer need post rename
        if (!$node instanceof Name) {
            return null;
        }
        /** @var Scope|null $scope */
        $scope = $node->getAttribute(AttributeKey::SCOPE);
        if ($node instanceof FullyQualified) {
            $result = $this->classRenamer->renameNode($node, $this->oldToNewClasses, $scope);
        } else {
            $result = $this->resolveResultWithPhpAttributeName($node, $scope);
        }
        if (!SimpleParameterProvider::provideBoolParameter(Option::AUTO_IMPORT_NAMES)) {
            return $result;
        }
        if (!$this->rootNode instanceof FileWithoutNamespace && !$this->rootNode instanceof Namespace_) {
            return $result;
        }
        $removedUses = $this->renamedClassesDataCollector->getOldClasses();
        $this->rootNode->stmts = $this->useImportsRemover->removeImportsFromStmts($this->rootNode->stmts, $removedUses);
        return $result;
    }
    /**
     * @param Node[] $nodes
     * @return Stmt[]
     */
    public function afterTraverse(array $nodes) : array
    {
        $this->renamedNameCollector->reset();
        return $nodes;
    }
    public function shouldTraverse(array $stmts) : bool
    {
        $this->oldToNewClasses = $this->renamedClassesDataCollector->getOldToNewClasses();
        return $this->oldToNewClasses !== [];
    }
    private function resolveResultWithPhpAttributeName(Name $name, ?Scope $scope) : ?FullyQualified
    {
        $phpAttributeName = $name->getAttribute(AttributeKey::PHP_ATTRIBUTE_NAME);
        if (\is_string($phpAttributeName)) {
            return $this->classRenamer->renameNode(new FullyQualified($phpAttributeName, $name->getAttributes()), $this->oldToNewClasses, $scope);
        }
        return null;
    }
    /**
     * @param Stmt[] $nodes
     * @return \Rector\PhpParser\Node\CustomNode\FileWithoutNamespace|\PhpParser\Node\Stmt\Namespace_|null
     */
    private function resolveRootNode(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($node instanceof FileWithoutNamespace || $node instanceof Namespace_) {
                return $node;
            }
        }
        return null;
    }
}
