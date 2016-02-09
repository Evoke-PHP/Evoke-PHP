<?php
/**
 * StubCallback
 *
 * @package   Evoke_Test\Network\URI\Processing\Rule
 */
namespace Evoke_Test\Network\URI\Processing\Rule;

class StubCallback
{
    protected $args;
    protected $argsStack;

    public function getArgs()
    {
        return $this->args;
    }

    public function getArgsStack()
    {
        return $this->argsStack;
    }

    public function setArgs()
    {
        $this->args        = func_get_args();
        $this->argsStack[] = $this->args;
    }
}
// EOF
