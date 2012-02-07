<?php
namespace Evoke\Core\Iface;

interface Model
{
	/// Get the data that the model represents.
	public function getData();
   
	/// Get the events used for processing in the model.
	public function getProcessingEvents();

	/// Notify the system of the data represented by the model.
	public function notifyData();
}
// EOF