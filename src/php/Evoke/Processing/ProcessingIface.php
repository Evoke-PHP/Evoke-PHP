<?php
namespace Evoke\Processing;

interface ProcessingIface
{
	public function getRequest();

	public function process();
}
// EOF