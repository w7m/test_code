<?php
/**
 * User: ebensaid
 * Date: 10/01/2019
 * Time: 12:32
 */

namespace App\EventSubscriber\EventsWorkflow;


use Doctrine\Common\Persistence\ObjectManager;

class EnteredListener
{
    private $manager;

    /**
     * EnteredListener constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     */
    public function onEntredTransition()
    {
        $this->manager->flush();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.folder_life_cycle.entered' => 'onEntredTransition',
        ];
    }
}