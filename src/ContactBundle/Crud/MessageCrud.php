<?php

namespace Perform\ContactBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\ContactBundle\Entity\Message;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageCrud extends AbstractCrud
{
    protected $routePrefix = 'perform_contact_message_';

    public function getActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
        ];
    }

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('name', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->add('email', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sent at',
                ],
            ])
            ->add('message', [
                'type' => 'text',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->setDefaultSort('createdAt', 'DESC')
            ;
    }

    public function configureFilters(FilterConfig $config)
    {
        $config->add('new', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_NEW);
            },
            'count' => true,
        ]);
        $config->add('archive', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_ARCHIVE);
            },
        ]);
        $config->add('spam', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_SPAM);
            },
        ]);
        $config->setDefault('new');
    }

    public function configureActions(ActionConfig $config)
    {
        $this->addViewAction($config);
        $config->add('perform_contact_archive');
        $config->add('perform_contact_new');
        $config->add('perform_contact_spam');
    }
}