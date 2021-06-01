<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Exception;

use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class SubjectNotValidException extends TransitionException
{
    public function __construct(object $subject, string $transitionName, WorkflowInterface $workflow, array $context)
    {
        parent::__construct($subject, $transitionName, $workflow, sprintf(
            'Unable to apply transition \'%s\' in workflow \'%s\' for an invalid subject of type \'%s\'.',
            $transitionName,
            $workflow->getName(),
            get_class($subject)
        ), $context);
    }
}
