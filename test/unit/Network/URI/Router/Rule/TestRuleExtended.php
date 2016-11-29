<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Rule;

class TestRuleExtended extends Rule
{
    public function getController() : string
    {
        return $this->uri;
    }

    public function isMatch() : bool
    {
        return true;
    }
}

