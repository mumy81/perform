<?php

namespace Perform\MailingListBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalSubscriberCrud extends AbstractCrud
{
    protected $routePrefix = 'perform_mailing_list_local_subscriber_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('firstName', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])
            ->add('email', [
                'type' => 'email',
            ])
            ->add('lists', [
                'type' => 'entity',
                'options' => [
                    'multiple' => true,
                    'class' => 'PerformMailingListBundle:LocalList',
                    'display_field' => 'name',
                ],
                'sort' => false,
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sign-up date',
                ],
            ])
            ;
    }
}