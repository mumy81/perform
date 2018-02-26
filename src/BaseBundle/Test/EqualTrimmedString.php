<?php

namespace Perform\BaseBundle\Test;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EqualTrimmedString extends \PHPUnit_Framework_Constraint
{
    public function __construct($value)
    {
        parent::__construct();
        if (!is_string($value)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'string');
        }

        $this->value = $value;
    }

    protected function trim($value)
    {
        return implode('', array_map('trim', explode(PHP_EOL, $value)));
    }

    public function matches($other)
    {
        return $this->trim($this->value) === $this->trim($other);
    }

    public function toString()
    {
        return sprintf('is equal to "%s"', $this->value);
    }
}
