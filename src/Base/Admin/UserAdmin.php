<?php

namespace Admin\Base\Admin;

/**
 * UserAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserAdmin extends AbstractAdmin
{
    protected $listFields = [
        'forename',
        'surname',
    ];
    protected $viewFields = [
        'forename',
        'surname',
    ];
    protected $createFields = [
        'forename',
        'surname',
    ];
}
