<?php

namespace ProjectArchitectureSniffer\Project\Zed;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class OrmNewEntityNotInCommunicationRule extends AbstractRule implements ClassAware
{
    public const RULE = 'Orm Entity can not be initialized in Zed Communication. Use Entity Manager!';

    protected const BUSINESS_AND_COMMUNICATION_PATTERN = '(^[\w]+\\\\Zed\\\\[\w]+\\\\Communication\\\\.+)';

    protected const ORM_ENTITY_PATTERN = '(^Orm\\\\Zed\\\\[\w]+\\\\Persistence\\\\(?!.*(?:(Query|TableMap))))';

    public function apply(AbstractNode $node): void
    {
        if (!preg_match(static::BUSINESS_AND_COMMUNICATION_PATTERN, $node->getFullQualifiedName())) {
            return;
        }

        foreach ($node->getMethods() as $methodNode) {
            $methodName = $methodNode->getImage();
            $allocatedExpressions = $methodNode->findChildrenOfType('AllocationExpression');

            foreach ($allocatedExpressions as $expression) {

                if ($expression->getImage() !== 'new') {
                    continue;
                }

                $reference = $expression->getFirstChildOfType('ClassReference');

                $referenceName = trim($reference->getName(), '\\');

                if (preg_match(static::ORM_ENTITY_PATTERN, $referenceName)) {
                    $message = sprintf(
                        'Entity `%s` initialized in method `%s`. %s',
                        $referenceName,
                        $methodName,
                        static::RULE,
                    );
                    $this->addViolation($methodNode, [$message]);
                }
            }
        }
    }
}
