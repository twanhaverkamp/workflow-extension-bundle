<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use TwanHaverkamp\WorkflowExtensionBundle\Exception\SubjectNotValidException;
use TwanHaverkamp\WorkflowExtensionBundle\Security\TransitionVoterInterface;

/**
 * This class subscribes on Workflow transition events and calls the right Voter.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class WorkflowTransitionEventSubscriber implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker) {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.transition' => ['validateSubject'],
        ];
    }

    /**
     * Validates the subject based on a {@see getVoterAttribute} supporting Voter.
     *
     * @throws SubjectNotValidException if the subject is invalid.
     */
    public function validateSubject(TransitionEvent $event): void
    {
        $context = $event->getContext();
        if (isset($context[TransitionVoterInterface::VALIDATE_SUBJECT]) === false ||
            $context[TransitionVoterInterface::VALIDATE_SUBJECT] === false
        ) {
            return;
        }

        $subject = $event->getSubject();
        if ($this->authorizationChecker->isGranted($this->getVoterAttribute($event), $subject) === false) {
            throw new SubjectNotValidException(
                $subject,
                $event->getTransition()->getName(),
                $event->getWorkflow(),
                $context
            );
        }
    }

    /**
     * Returns a 'workflow.[WORKFLOW_NAME].transition.[TRANSITION_NAME]' formatted Voter attribute.
     */
    private function getVoterAttribute(TransitionEvent $event): string
    {
        return sprintf('workflow.%s.transition.%s',
            $event->getWorkflow()->getName(),
            $event->getTransition()->getName()
        );
    }
}
