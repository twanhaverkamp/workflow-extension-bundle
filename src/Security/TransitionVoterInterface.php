<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Security;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface TransitionVoterInterface
{
    /**
     * If you pass this constant as a context key while applying a transition, a Voter which supports
     * your Workflow and Transition will validate your subject.
     *
     * @var string
     */
    public const VALIDATE_SUBJECT = 'validate_subject';

    /**
     * Return the class of the subject which should be validated.
     */
    public function getSubjectClass(): string;

    /**
     * Return the Transition name to validate.
     */
    public function getTransitionName(): string;

    /**
     * Return the Workflow name to validate.
     */
    public function getWorkflowName(): string;
}
