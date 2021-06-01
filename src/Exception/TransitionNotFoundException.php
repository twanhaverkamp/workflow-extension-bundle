<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Exception;

use Symfony\Component\Workflow\Exception\LogicException;
use TwanHaverkamp\WorkflowExtensionBundle\Model\TransitionableInterface;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class TransitionNotFoundException extends LogicException
{
    public function __construct(string $workflowName, TransitionableInterface $subject)
    {
        parent::__construct(sprintf(
            'Unable to find an enabled Transition in Workflow \'%s\' for subject of type \'%s\' with current place \'%s\'.',
            $workflowName,
            get_class($subject),
            $subject->getCurrentPlace()
        ));
    }
}
