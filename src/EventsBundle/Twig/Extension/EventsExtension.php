<?php

namespace Admin\EventsBundle\Twig\Extension;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * EventsExtension
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventsExtension extends \Twig_Extension
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('upcomingEvents', [$this, 'getUpcoming']),
        ];
    }

    public function getUpcoming($limit = 5)
    {
        return $this->entityManager
            ->getRepository('AdminEventsBundle:Event')
            ->findUpcoming($limit);
    }

    public function getName()
    {
        return 'events';
    }
}
