<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * TestAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TestAdmin extends AbstractAdmin
{
    public function configureTypes(TypeConfig $config)
    {
    }
}