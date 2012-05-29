<?php
namespace Evoke\View\Form\Input;

class File extends \Evoke\View
{ 
	/** Construct a button/input that will enable styling to be done on the ugly
	 *  file input.  With our CSS we can control the behaviour by overlapping the
	 *  button with the input.  Smart things can then be done such as:
	 *  \verbatim
	 1. Within a standard form:
	 NOSCRIPT   - Display the button only with the file input hidden so
	 that clicks only go to the button.  When the button is clicked any
	 previously entered data in the current form should be submitted
	 and the user should be sent to an area where they can upload files.
	 JAVASCRIPT - Display both, but with the file input opacity 0% so that
	 only the pretty button is seen.  Deal with the change of the
	 overlying file input which was the one that was clicked and save
	 any form data using AJAX before submitting the file.
	 2. Within a form set for file input data:
	 NOSCRIPT   - Display the input file only and just leave everything as
	 standard HTML.
	 JAVASCRIPT - Leave the input file ugly, but still use AJAX to save
	 form data before submitting the file.\endverbatim
	 *
	 *  The input file must be the one clicked by the user to bring up the file
	 *  dialog and therefore should always cover the button.  This is because we
	 *  cannot make javascript send a click event to the input of type file due
	 *  to perceived security risks in firefox < 4 and possibly others.  To
	 *  display the button the opacity of the input file should be set to 0%.
	 */
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		/// The default setup assumes setup 1 as above in the construct comment.
		$setup += array(
			'Button_Attribs'    => array('class' => 'Input_File Button Good',
			                             'type'  => 'submit',
			                             'name'  => 'Input_File_Redirect'),
			'Button_Text'       => NULL,
			'Default_Attribs'   => array('class' => 'Input_File_Container'),
			'Input_Attribs'     => array('class' => 'Input_File Hidden',
			                             'type'  => 'file',
			                             'size'  => 7),
			'Request_Alias'     => NULL,
			'Request_Prefix'    => 'Input_File',
			'Request_Separator' => '_');

		parent::__construct($setup);
      
		if (!isset($this->buttonText))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Button_Text');
		}

		$this->buttonAttribs['value'] = $this->buttonText;

		if (!isset($this->inputAttribs['name']))
		{
			$this->inputAttribs['name'] = $this->requestPrefix;

			if (isset($this->requestAlias))
			{
				$this->inputAttribs['name'] .=
					$this->requestSeparator . $this->requestAlias;
			}
		}
      
		parent::set(
			array('div',
			      array(),
			      array('Children' => array(
				            array('input', $this->buttonAttribs),
				            array('input', $this->inputAttribs)))));
	}
}
// EOF