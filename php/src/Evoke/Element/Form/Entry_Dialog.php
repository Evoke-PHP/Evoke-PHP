<?php
namespace Evoke\Element\Form;

class EntryDialog extends Entry
{
	public function __construct($setup=array())
	{
		// By default, dialogs are wrapped by their own form element.
		$setup = array_merge(
			array('Attribs' => array('class'  => 'Entry Dialog Info',
			                         'action' => '',
			                         'method' => 'post'),
			      'Options' => array('Finish' => false,
			                         'Start' => false)),
			$setup);
      
		parent::__construct($setup);

		/// \todo Fix this class.
		throw new Exception(__METHOD__ . ' needs update to new element class');
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
				new Element_Submit(
					array('class' => 'Dialog_Submit Button Good Small',
					      'name'  => $this->tableName . '_Add',
					      'value' => $this->translator->get('Add'))));
		}
		else
		{
			$submitButtons = array(
				new Element_Submit(
					array('class' => 'Dialog_Submit Button Info Small',
					      'name'  => $this->tableName . '_Modify',
					      'value' => $this->translator->get('Edit'))));
		}
	    
		$submitButtons[] = new Element_Submit(
			array('class' => 'Dialog_Cancel Button Bad Small',
			      'name'  => $this->tableName . '_Cancel',
			      'value' => $this->translator->get('Cancel')));
      
		$this->addElement(array('div',
		                        $this->submitButtonAttribs,
		                        $submitButtons));
	}
}
// EOF