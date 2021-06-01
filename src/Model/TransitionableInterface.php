<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Model;

/**
 * Classes implementing this interface are supported by Workflows
 * with 'marking_store' type 'method' and property 'currentPlace'.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
interface TransitionableInterface
{
    public function getCurrentPlace(): ?string;

    public function setCurrentPlace(?string $place): void;
}
