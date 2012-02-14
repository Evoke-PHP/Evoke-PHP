<?php
namespace Evoke\Core\Iface;

interface Model
{
	/// Get the data that the model represents.
	public function getData();
   
	/// Notify the system of the data represented by the model.
	public function notifyData();
}
// EOF