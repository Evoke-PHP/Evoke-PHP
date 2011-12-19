<?php

require_once 'element.php';
require_once 'element/dialog.php';
require_once 'element/submit.php';
require_once 'translator.php';
require_once 'xhtml_writer.php';

$tr = Translator::getInstance();
$xwr = XHTML_Writer::getInstance();

$confirmDeleteDialog = new Element_Dialog(
   array('Buttons' => array(
	    new Element_Submit(
	       array('class' => 'Dialog_Submit Button Bad Small',
		     'name'  => 'Confirm',
		     'value' => $tr->get('Confirm'))),
	    new Element_Submit(
	       array('class' => 'Dialog_Cancel Button Info Small',
		     'name'  => 'Cancel',
		     'value' => $tr->get('Cancel')))),
	 'Content' => array(
	    'div',
	    array('class' => 'Confirm_Delete_Text'),
	    array('Text' => $tr->get('Confirm_Delete_Text'))),
	 'Heading_Text' => $tr-> get('Confirm_Delete_Heading')));
$confirmDeleteDialog->write($xwr);
$xwr->writeXHTML();

// EOF