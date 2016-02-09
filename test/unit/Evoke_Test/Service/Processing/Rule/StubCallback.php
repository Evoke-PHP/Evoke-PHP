<?php
/**
 * StubCallback
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */
namespace Evoke_Test\Service\Processing\Rule;

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
