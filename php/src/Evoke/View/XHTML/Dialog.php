<?php
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface;

class Dialog implements ViewIface
{
	public function __construct(Array $setup)
	{
		/// \todo Fix this to the new view interface.
		throw new \RuntimeException(__METHOD__ . ' todo fix implementation.');
		
		$setup += array('Buttons'         => array(),
		                'Buttons_Attribs' => array('class' => 'Buttons'),
		                'Content_Attribs' => array('class' => 'Content'),
		                'Default_Attribs' => array('class'  => 'Dialog',
		                                           'action' => '',
		                                           'method' => 'POST'),
		                'Heading_Attribs' => array('class' => 'Heading'));

		parent::__construct($setup);
	}

	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $data = array())
	{
		$data += array('Buttons'      => array(),
		               'Content'      => NULL,
		               'Form_Attribs' => array(),
		               'Heading'      => NULL);

		if (!isset($data['Content']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Content');
		}

		if (!isset($data['Heading']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Heading');
		}
      
		$dialogItems = array(
			array('div',
			      $this->headingAttribs,
			      array('Text' => $data['Heading'])),
			array('div',
			      $this->contentAttribs,
			      array('Text' => $data['Content'])));

		if (!empty($data['Buttons']))
		{
			$dialogItems[] = array('div',
			                       $this->buttonsAttribs,
			                       $data['Buttons']);
		}
      
		parent::__construct(array('form',
		                          $data['Form_Attribs'],
		                          $dialogItems));
	}
}
// EOF