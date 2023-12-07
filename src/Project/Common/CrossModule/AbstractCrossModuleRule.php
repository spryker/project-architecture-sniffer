<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;

/**
 * @SuppressWarnings(PHPMD.ClassIsNotCrossModuleExtendedRule)
 */
class AbstractCrossModuleRule extends AbstractRule
{
    protected const CLASSES_ALLOWED_TO_USE_CROSSMODULE = [
        '/DependencyProvider$/',
        '/^Pyz\\\\Zed\\\\[A-Za-z]*DataImport\\\\/',
    ];

    protected const CLASSES_ALLOWED_TO_BE_USED = [
        '/^(self|static|parent)$/',
        '/^\\\\(Generated|Symfony|Elastica|Orm|League|Propel|Twig|GuzzleHttp|Mpdf|OpenIDConnectServer|Ramsey|PDepend|'
            . 'Spryker\\\\Glue\\\\GlueApplication|'
            . 'Spryker\\\\DecimalObject|'
            . 'Spryker\\\\Service\\\\UtilText|'
            . 'Spryker\\\\Shared\\\\Kernel|'
            . 'Spryker\\\\Zed\\\\PropelOrm'
            . ')\\\\/',
        '/Exception$/',
        '/^\$/',
        '/Elasticsearch|Search/',
        '/^\\\\Lcobucci\\\\Clock\\\\/',
        '/^\\\\('
            . 'DateTime|'
            . 'ReflectionClass|'
            . 'DateTimeZone|'
            . 'DateTimeInterface|'
            . 'DatePeriod|'
            . 'ArrayObject|'
            . 'DateInterval|'
            . 'DateTimeImmutable|'
            . 'IntlDateFormatter|'
            . 'stdClass|'
            . 'Swift_Attachment|'
            . 'PDO|'
            . 'SimpleXMLElement|'
            . 'Spryker\\\\Glue\\\\GlueApplication\\\\Rest\\\\Request\\\\Data\\\\Page|'
            . 'Spryker\\\\Zed\\\\DataImport\\\\Business\\\\Model\\\\DataSet\\\\DataSet|'
            . 'Spryker\\\\Glue\\\\GlueApplication\\\\Rest\\\\Collection\\\\ResourceRouteCollection|'
            . 'Pyz\\\\Glue\\\\GlueApplication\\\\Router\\\\CustomRouteRouter\\\\AldiRoute|'
            . 'Pyz\\\\Zed\\\\DataImport\\\\Business\\\\Model\\\\DataSetImporter'
            . ')$/',
    ];

    public function apply(AbstractNode $node): void
    {
    }

    public function getDescription(): string
    {
        return 'You can\'t use crossmodule here';
    }

    protected function crossModuleViolationExists(AbstractNode $node, string $fullClassName): bool
    {
        $parentPath = $this->convertNameToPath($node->getNamespaceName());
        $childPath = $this->convertNameToPath($fullClassName);

        if (!$this->isLayerTheSame($parentPath, $childPath) || !$this->isModuleTheSame($parentPath, $childPath)) {
            return true;
        }

        return false;
    }

    protected function isLayerTheSame(array $parentPath, array $childPath): bool
    {
        if ($parentPath[1] === $childPath[1]) {
            return true;
        }

        if ($childPath[1] === 'Shared') {
            return true;
        }

        return false;
    }

    protected function isModuleTheSame(array $parentPath, array $childPath): bool
    {
        if ($parentPath[2] === $childPath[2]) {
            return true;
        }

        $iso2Code = str_replace($childPath[2], '', $parentPath[2]);
        if (preg_match('/^[A-Z]{2}$/', $iso2Code)) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string>
     */
    protected function convertNameToPath(string $name): array
    {
        $path = $this->explodeName($name);
        $path = $this->fillMissedLevels($path);

        return $path;
    }

    /**
     * @return array<string>
     */
    protected function explodeName(string $name): array
    {
        $name = preg_replace('/^\\\\/', '', $name);

        return explode('\\', $name);
    }

    /**
     * @param array<string> $path
     *
     * @return array<string>
     */
    protected function fillMissedLevels(array $path): array
    {
        if (!array_key_exists(1, $path)) {
            $path[1] = '';
        }

        if (!array_key_exists(2, $path)) {
            $path[2] = '';
        }

        return $path;
    }

    protected function isClassAllowedToUseCrossModule(string $namespace, string $className): bool
    {
        foreach (static::CLASSES_ALLOWED_TO_USE_CROSSMODULE as $classRegExp) {
            if (preg_match($classRegExp, $namespace . '\\' . $className)) {
                return true;
            }
        }

        return false;
    }

    protected function isClassAllowedToBeUsed(string $fullClassName): bool
    {
        foreach (static::CLASSES_ALLOWED_TO_BE_USED as $classRegExp) {
            if (preg_match($classRegExp, $fullClassName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<\PHPMD\Node\ASTNode> $classOrInterfaceReference
     */
    protected function findClosestClass(array $classOrInterfaceReference, AbstractNode $node): ?AbstractNode
    {
        $closestClass = null;
        foreach ($classOrInterfaceReference as $class) {
            if ($node->getNode()->getStartLine() !== $class->getNode()->getStartLine()) {
                continue;
            }

            if ($node->getNode()->getStartColumn() < $class->getNode()->getStartColumn()) {
                continue;
            }

            $closestClass = $class;
        }

        return $closestClass;
    }
}
