<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TwanHaverkamp\WorkflowExtensionBundle\Exception\RedirectStrategyNotFoundException;
use TwanHaverkamp\WorkflowExtensionBundle\Exception\SubjectNotValidException;
use TwanHaverkamp\WorkflowExtensionBundle\Exception\TransitionNotFoundException;
use TwanHaverkamp\WorkflowExtensionBundle\Model\TransitionableInterface;
use TwanHaverkamp\WorkflowExtensionBundle\Security\TransitionVoterInterface;

/**
 * Note:
 * - The controller's route name MUST match the {@see getTransitionName} value.
 * - To get your subject validated, you need to create a custom {@see TransitionVoterInterface}.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
abstract class AbstractTransitionController extends AbstractController implements TransitionControllerInterface
{
    /**
     * Returns whether you are allowed to view the supported Transition.
     */
    final protected function canViewTransition(TransitionableInterface $subject): bool
    {
        return $this
            ->getWorkflow()
            ->can($subject, $this->getTransitionName());
    }

    /**
     * Applies the supported Transition.
     *
     * @throws SubjectNotValidException If a supporting {@see TransitionVoterInterface} invalidates the subject.
     */
    final protected function applyTransition(TransitionableInterface $subject): void
    {
        $this
            ->getWorkflow()
            ->apply($subject, $this->getTransitionName(), [
                TransitionVoterInterface::VALIDATE_SUBJECT => true
            ]);
    }

    /**
     * Returns a {@see RedirectResponse} if an enabled Transition can be found for the subject.
     *
     * @throws TransitionNotFoundException       if the subject has no enabled Transition.
     * @throws RedirectStrategyNotFoundException if an invalid redirect strategy is provided.
     */
    final protected function redirectToEnabledTransition(
        TransitionableInterface $subject,
        string $strategy = TransitionControllerInterface::REDIRECT_STRATEGY_FIRST_AVAILABLE,
        array $parameters = []
    ): RedirectResponse {
        $workflow = $this->getWorkflow();
        $enabledTransitions = $workflow->getEnabledTransitions($subject);

        switch ($strategy) {
            case TransitionControllerInterface::REDIRECT_STRATEGY_FIRST_AVAILABLE:
                reset($enabledTransitions);
                break;
            case TransitionControllerInterface::REDIRECT_STRATEGY_LAST_AVAILABLE:
                end($enabledTransitions);
                break;
            default:
                throw new RedirectStrategyNotFoundException($strategy);
        }

        $enabledTransition = current($enabledTransitions);
        if ($enabledTransition === false) {
            throw new TransitionNotFoundException($workflow->getName(), $subject);
        }

        return $this->redirectToRoute($enabledTransition->getName(), $parameters);
    }
}
