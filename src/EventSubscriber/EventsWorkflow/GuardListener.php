<?php
/**
 * User: ebensaid
 * Date: 10/01/2019
 * Time: 12:11
 */

namespace App\EventSubscriber\EventsWorkflow;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class GuardListener implements EventSubscriberInterface
{
    private $checker;

    /**
     * GuardListener constructor.
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @param GuardEvent $event
     */
    public function onTransition(GuardEvent $event)
    {
        if (!$this->checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $event->setBlocked(true);
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function onTransitionExpertChecker(GuardEvent $event)
    {
        if (!$this->checker->isGranted('ROLE_EXPERT')) {
            $event->setBlocked(true);
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function onTransitionValidatorChecker(GuardEvent $event)
    {
        if (!$this->checker->isGranted('ROLE_VALIDATOR')) {
            $event->setBlocked(true);
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function onTransitionFinancialChecker(GuardEvent $event)
    {
        if (!$this->checker->isGranted('ROLE_FINANCIAL')) {
            $event->setBlocked(true);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.folder_life_cycle.guard' => 'onTransition',
            'workflow.folder_life_cycle.guard.treat' => 'onTransitionExpertChecker',
            'workflow.folder_life_cycle.guard.write-sales-contract' => 'onTransitionExpertChecker',
            'workflow.folder_life_cycle.guard.write-wreck-report' => 'onTransitionExpertChecker',
            'workflow.folder_life_cycle.guard.submit-folder' => 'onTransitionExpertChecker',

            'workflow.folder_life_cycle.guard.reassign' => 'onTransitionValidatorChecker',
            'workflow.folder_life_cycle.guard.to-reconsider' => 'onTransitionValidatorChecker',
            'workflow.folder_life_cycle.guard.validate-submission' => 'onTransitionValidatorChecker',
            'workflow.folder_life_cycle.guard. validate-wreck-report' => 'onTransitionValidatorChecker',

            'workflow.folder_life_cycle.guard.validate-refund' => 'onTransitionFinancialChecker',
        ];
    }
}
