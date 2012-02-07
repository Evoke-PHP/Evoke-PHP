<?php
namespace Evoke\Element\Form;
/** XHTML entry form for a databse record.
 *  Provide an XHTML form to show and allow modification to database tables.
 */
class Upload extends Base
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
		parent::__construct();

		// Merge giving preference to values specified in the constructor.
		$this->uploadSetup = array_merge($this->uploadSetup, $uploadSetup);

		$this->setupFormArr($formSetup);

		// Set it to be an upload form.
		$this->formSetup['Form_Attribs']['enctype'] = 'multipart/form-data';
	}

	/**************************************/
	/* Public Methods - There Aren't Any! */
	/**************************************/

	// The following public base class functions are used to control the form.
	//      write - To output the form.
	//      resetElements - To clear the form elements.
	//      setupForm - To change settings for the form.

	/*********************/
	/* Protected Methods */
	/*********************/

	/// Set the elements for the entry form.
	protected function setElements()
	{
		$uploadTitle = array(
			'div',
			array('class' => 'Upload_Title'),
			array('Children' =>
			      array(
				      array(
					      'span',
					      array('class' => 'Upload_Title_Text'),
					      array('Text' => $this->uploadSetup['Title_Text'])),
				      array(
					      'span',
					      array('class' => 'Upload_Title_Warning'),
					      array('Text' => $this->uploadSetup['Title_Warning'])))));

		$uploadInput = array(
			'div',
			array('class' => 'Upload_Input'),
			array('Children' =>
			      array(
				      array(
					      'input',
					      array('type' => 'file',
					            'id'   => $this->uploadSetup['Upload_ID'],
					            'name' => $this->uploadSetup['Upload_File'],
					            'size' => $this->uploadSetup['Upload_Field_Size'])),
				      array(
					      'input',
					      array('type' => 'submit',
					            'value' => $this->uploadSetup['Upload_Button'])))));

		$upload = array(
			'div',
			array('class' => 'Form_Upload'),
			array('Children' =>
			      array($uploadTitle, $uploadInput)));

		$this->addElement($upload);
	}
}
// EOF