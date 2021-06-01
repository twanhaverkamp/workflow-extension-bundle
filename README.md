# Workflow Extension Bundle

## Introduction
This bundle extends Symfony's [Workflow] component with a set of tools that makes it possible to build Controllers
based on a Workflow configuration that tell your users where they can (and cannot) go with the business logic being
handled by Security Voters.

## Getting started
For this example, let's assume you're building an e-commerce platform where customers need to enter personal
information, select a payment method, and make a delivery choice.

Now we don't want our customers to have the option of a delivery choice while they haven't entered any personal
details yet, do we?

## Installation
This bundle is available as [Composer] package.

```console
$ composer require twanhaverkamp/workflow-extension-bundle
```

> ***Note:** If you don't use Symfony Flex, don't forget to add this bundle to the list of registered bundles in
the `Kernel.php` file of your project.*

### 1. Workflow configuration
Let's configure an e-commerce Workflow which consists the following steps:
- Enter personal information
- Select a payment method
- Select a delivery method

```yaml
...
workflows:
  ecommerce:
    type: 'state_machine'
    marking_store:
      type: 'method'
      property: 'currentPlace'
    supports:
      - App\Model\Checkout
    initial_marking: new
    places:
      - new
      - personal_information_entered
      - payment_method_selected
      - delivery_method_selected
    transitions:
      enter_personal_information:
        from:
          - new

          # Allow places further down the workflow in this transition.
          - payment_method_selected
          - delivery_method_selected
        to: personal_information_entered
      select_payment_method:
        ...
      select_delivery_method:
        ...
...
```
> ***Note:** If you're not familiar with the [Workflow] component, I would recommend reading its documentation.*

> ***Note:** Make sure your supported class implements `TwanHaverkamp\WorkflowExtensionBundle\Model\TransitionableInterface`.*

### 2. Controllers and Routes
For every Transition we have configured we need to create a Controller which extends the
`TwanHaverkamp\WorkflowExtensionBundle\Controller\AbstractTransitionController`.

```php
<?php

namespace App\Controller;

use App\Model\Checkout;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\WorkflowInterface;
use TwanHaverkamp\WorkflowExtensionBundle\Controller\AbstractTransitionController;
use TwanHaverkamp\WorkflowExtensionBundle\Exception\SubjectNotValidException;

class PaymentMethodController extends AbstractTransitionController
{
    private WorkflowInterface $workflow;
    
    public function __construct(WorkflowInterface $checkoutWorkflow)
    {
        $this->workflow = $checkoutWorkflow;
    }
    
    public function getTransitionName(): string
    {
        return 'select_payment_method';
    }
    
    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }
    
    public function index(Checkout $checkout): Response
    {
        // Note: Let's assume we autowired the $checkout variable with a ParamConverter.
        
        if ($this->canViewTransition($checkout) === false) {
            return $this->redirectToEnabledTransition($checkout);
        }
        
        // Here's where you create your page components like a form which handles the selection of a payment method.
    
        try {
            $this->applyTransition($checkout);
        } catch (SubjectNotValidException $exception) {
            // Pass error messages to your response.
        }
    
        // Return your template and parameters.
    }
}
``` 

### 3. Transition Voters
Since it should not be the controller's responsibility to determine whether a customer is allowed to visit the page or
perform a Transition, we use Symfony's [Security] component. We are going to create a new Voter that will check if our
`App\Model\Checkout` class contains a valid payment method.

```php
<?php

namespace App\Security;

use App\Model\Checkout;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use TwanHaverkamp\WorkflowExtensionBundle\Security\AbstractTransitionVoter;

class PaymentMethodVoter extends AbstractTransitionVoter
{
    public function getSubjectClass(): string
    {
        return Checkout::class;
    }

    public function getTransitionName(): string
    {
        return 'select_payment_method';
    }

    public function getWorkflowName(): string
    {
        return 'checkout';
    }

    /**
     * @param Checkout $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        // Check here whether the Checkout model contains a valid payment method
        // and the customer can apply this Transition.
    }
}
```

[Composer]: https://getcomposer.org/doc/00-intro.md
[Security]: https://symfony.com/doc/current/components/security.html
[Workflow]: https://symfony.com/doc/current/components/workflow.html
