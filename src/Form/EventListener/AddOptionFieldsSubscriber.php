<?php


namespace App\Form\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddOptionFieldsSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        //$entity = $event->getData();

        $form->add('dataSourceOptions', TextType::class);
    }
}
