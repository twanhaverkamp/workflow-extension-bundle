<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class AbstractTransitionVoter extends Voter implements TransitionVoterInterface
{
    /**
     * {@inheritdoc}
     */
    final protected function supports(string $attribute, $subject): bool
    {
        return $attribute === sprintf(
            'workflow.%s.transition.%s',
            $this->getWorkflowName(),
            $this->getTransitionName()
        ) && is_subclass_of($subject, $this->getSubjectClass(), false) === true;
    }
}
