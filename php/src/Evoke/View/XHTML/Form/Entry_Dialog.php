<?php
namespace Evoke\View\Form;

class EntryDialog extends Entry
{
	public function __construct($setup=array())
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		// By default, dialogs are wrapped by their own form element.
		$setup = array_merge(
			array('Attribs' => array('class'  => 'Entry Dialog Info',
			                         'action' => '',
			                         'method' => 'post'),
			      'Options' => array('Finish' => false,
			                         'Start' => false)),
			$setup);
      
		parent::__construct($setup);
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Build the buttons that are contained in the form.
	protected function buildFormButtons()
	{
		if (empty($this->fieldValues))
		{
			$submitButtons = array(
				new View_Submit(
					array('class' => 'Dialog_Submit Button Good Small',
					      'name'  => $this->tableName . '_Add',
					      'value' => $this->translator->get('Add'))));
		}
		else
		{
			$submitButtons = array(
				new View_Submit(
					array('class' => 'Dialog_Submit Button Info Small',
					      'name'  => $this->tableName . '_Modify',
					      'value' => $this->translator->get('Edit'))));
		}
	    
		$submitButtons[] = new View_Submit(
			array('class' => 'Dialog_Cancel Button Bad Small',
			      'name'  => $this->tableName . '_Cancel',
			      'value' => $this->translator->get('Cancel')));
      
		$this->addView(array('div',
		                        $this->submitButtonAttribs,
		                        $submitButtons));
	}
}
// EOF