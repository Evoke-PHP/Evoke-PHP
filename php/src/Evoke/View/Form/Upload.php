<?php
namespace Evoke\View\XHTML\Form;

/**
 * Upload
 *
 * XHTML entry form for a databse record.
 * Provide an XHTML form to show and allow modification to database tables.
 *
 * @todo Fix to the new view interface.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Upload extends Form
{
	private $uploadSetup =
		array('Title_Text' => 'File Upload',
		      'Title_Warning' => 'Max Filesize: ',
		      'Upload_ID' => 'Upload_File',
		      'Upload_File' => 'Upload_File',
		      'Upload_Field_Size' => 40,
		      'Upload_Button' => 'Upload');

	/// Construct the entry form with the table information.
	public function __construct($uploadSetup = array(),
	                            $formSetup = array())
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');
		parent::__construct();

		// Merge giving preference to values specified in the constructor.
		$this->uploadSetup = array_merge($this->uploadSetup, $uploadSetup);

		$this->setupFormArr($formSetup);

		// Set it to be an upload form.
		$this->formSetup['Form_Attribs']['enctype'] = 'multipart/form-data';
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/// Set the elements for the entry form.
	protected function setElements()
	{
		$uploadTitle = array(
			'div',
			array('class' => 'Upload_Title'),
			array(array('span',
			            array('class' => 'Upload_Title_Text'),
			            $this->uploadSetup['Title_Text']),
			      array('span',
			            array('class' => 'Upload_Title_Warning'),
			            $this->uploadSetup['Title_Warning'])));

		$uploadInput = array(
			'div',
			array('class' => 'Upload_Input'),
			array(array('input',
			            array('type' => 'file',
			                  'id'   => $this->uploadSetup['Upload_ID'],
			                  'name' => $this->uploadSetup['Upload_File'],
			                  'size' => $this->uploadSetup['Upload_Field_Size'])),
			      array('input',
			            array('type' => 'submit',
			                  'value' => $this->uploadSetup['Upload_Button']))));

		$upload = array(
			'div',
			array('class' => 'Form_Upload'),
			array($uploadTitle, $uploadInput));

		$this->addElement($upload);
	}
}
// EOF