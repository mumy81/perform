<?php

namespace Perform\TeamBundle\Menu;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;

/**
 * TeamLinkProvider.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamLinkProvider implements LinkProviderInterface
{
    public function addLinks(ItemInterface $menu)
    {
        $menu->addChild('team', [
            'route' => 'perform_team_team_list',
        ])->setExtra('icon', 'users');
    }
}