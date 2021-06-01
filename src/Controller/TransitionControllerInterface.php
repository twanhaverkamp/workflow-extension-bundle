<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Controller;

use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface TransitionControllerInterface
{
    /**
     * With this strategy you will be redirected to the earliest available Transition in the supported Workflow.
     *
     * @var string
     */
    public const REDIRECT_STRATEGY_FIRST_AVAILABLE = 'first_available';

    /**
     * With this strategy you will be redirected to the furthest available Transition in the supported Workflow.
     *
     * @var string
     */
    public const REDIRECT_STRATEGY_LAST_AVAILABLE = 'last_available';

    /**
     * Return the Transition name which the controller represents.
     */
    public function getTransitionName(): string;

    /**
     * Return the Workflow instance which the controller represents.
     */
    public function getWorkflow(): WorkflowInterface;
}
