<?php

namespace TwanHaverkamp\WorkflowExtensionBundle\Exception;

use LogicException;

/**
 * @author Twan Haverkamp <twan.haverkamp@outlook.com>
 */
final class RedirectStrategyNotFoundException extends LogicException
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf(
            'Unable to find a redirect strategy of type \'%s\'.',
            $type
        ));
    }
}
