<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Model;

/**
 * Classes using this trait meet the requirements of {@see TransitionableInterface}.
 *
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
trait TransitionableTrait
{
    private ?string $place;

    public function getCurrentPlace(): ?string
    {
        return $this->place;
    }

    public function setCurrentPlace(?string $place): void
    {
        $this->place = $place;
    }
}
