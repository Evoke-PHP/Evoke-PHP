<?php
/**
 * File Input View
 *
 * @package View\Form\Input
 */
namespace Evoke\View\Form\Input;

use LogicException;

/**
 * File Input View
 *
 * A button/input that will enable styling to be done on the ugly file input.
 * With our CSS we can control the behaviour by overlapping the button with the
 * input.  Smart things can then be done such as:
 * 
 * 1. Within a standard form:
 *     NOSCRIPT   - Display the button only with the file input hidden so that
 *                  clicks only go to the button.  When the button is clicked
 *                  any previously entered data in the current form should be
 *                  submitted and the user should be sent to an area where they
 *                  can upload files.
 *     JAVASCRIPT - Display both, but with the file input opacity 0% so that
 *                  only the pretty button is seen.  Deal with the change of the
 *                  overlying file input which was the one that was clicked and
 *                  save any form data using AJAX before submitting the file.
 * 2. Within a form set for file input data:
 *     NOSCRIPT   - Display the input file only and just leave everything as
 *                  standard HTML.
 *     JAVASCRIPT - Leave the input file ugly, but still use AJAX to save form
 *                  data before submitting the file.
 *
 * The input file must be the one clicked by the user to bring up the file
 * dialog and therefore should always cover the button.  This is because we
 * cannot make javascript send a click event to the input of type file due
 * to perceived security risks in firefox < 4 and possibly others.  To
 * display the button the opacity of the input file should be set to 0%.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class File extends View
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the file input.
	 *
	 * @return mixed[] The file input.
	 */
	public function get()
	{
		if (!isset($this->params['Button_Text']))
		{
			throw new LogicException('needs Button_Text');
		}
		
		return array(
			'div',
			array('class' => 'Input_File_Container'),
			array(array('input',
			            array('class' => 'Input_File Button Good',
			                  'type'  => 'submit',
			                  'name'  => 'Input_File_Redirect',
			                  'value' => $this->params['Button_Text'])),
			      array('input',
			            array('class' => 'Input_File Hidden',
			                  'type'  => 'file',
			                  'size'  => 7,
			                  'name'  => 'Input_File'))));
	}
}
// EOF