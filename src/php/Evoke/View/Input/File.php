<?php
/**
 * File Input View
 *
 * @package View
 */
namespace Evoke\View\Form\Input;

use Evoke\View\ViewIface,
	InvalidArgumentException;

/**
 * Input File
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class File implements ViewIface
{
	/**
	 * Construct a button/input that will enable styling to be done on the ugly
	 * file input.  With our CSS we can control the behaviour by overlapping the
	 * button with the input.  Smart things can then be done such as:
	 * 
	 * 1. Within a standard form:
	 *     NOSCRIPT   - Display the button only with the file input hidden so
	 *                  that clicks only go to the button.  When the button is
	 *                  clicked any previously entered data in the current form
	 *                  should be submitted and the user should be sent to an
	 *                  area where they can upload files.
	 *     JAVASCRIPT - Display both, but with the file input opacity 0% so that
	 *                  only the pretty button is seen.  Deal with the change of
	 *                  the overlying file input which was the one that was
	 *                  clicked and save any form data using AJAX before
	 *                  submitting the file.
	 * 2. Within a form set for file input data:
	 *     NOSCRIPT   - Display the input file only and just leave everything as
	 *                  standard HTML.
	 *     JAVASCRIPT - Leave the input file ugly, but still use AJAX to save
	 *               	form data before submitting the file.
	 *
	 * The input file must be the one clicked by the user to bring up the file
	 * dialog and therefore should always cover the button.  This is because we
	 * cannot make javascript send a click event to the input of type file due
	 * to perceived security risks in firefox < 4 and possibly others.  To
	 * display the button the opacity of the input file should be set to 0%.
	 *
	 * @param string  The text for the file upload button.
	 * @param mixed[] The overlaid button attributes.
	 * @param mixed[] Attributes for the container that holds the button and
	 *                input.
	 * @param mixed[] The input attributes.
	 */
	public function __construct(
		/* string */ $buttonText,
		Array        $buttonAttribs    = array(
			'class' => 'Input_File Button Good',
			'type'  => 'submit',
			'name'  => 'Input_File_Redirect'),
		Array        $containerAttribs = array(
			'class' => 'Input_File_Container'),
		Array        $inputAttribs     = array('class' => 'Input_File Hidden',
		                                       'type'  => 'file',
		                                       'size'  => 7,
		                                       'name'  => 'Input_File'))
	{
		if (!isset($buttonText))
		{
			throw new InvalidArgumentException('needs Button_Text');
		}

		$this->buttonText       = $buttonText;
		$this->buttonAttribs    = $buttonAttribs;
		$this->containerAttribs = $containerAttribs;
		$this->inputAttribs     = $inputAttribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the file input.
	 *
	 * @param mixed[] Parameters to the view.
	 *
	 * @return mixed[] The file input.
	 */
	public function get(Array $params = array())
	{
		return array('div',
		             $this->containerAttribs,
		             array('Children' => array(
			                   array('input', $this->buttonAttribs),
			                   array('input', $this->inputAttribs))));
	}
}
// EOF