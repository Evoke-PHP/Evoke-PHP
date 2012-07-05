<?php
namespace Evoke\View\XHTML\Input;

/**
 * Foreign DB Input
 *
 * @todo Check whether this class is obsolete.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Foreign extends DB
{
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');
      
		$setup = array_merge(
			array('Data'             => NULL,
			      'Field'            => 'UNSET_FIELD',
			      'Field_Info'       => NULL,
			      'Field_Values'     => array(),
			      'Foreign_Selector' => array('Append_Data'  => array(),
			                                  'Prepend_Data' => array()),
			      'Selected_Fields'  => array(
				      'Field'          => 'SF_UNSET_FIELD',
				      'Selector_Field' => 'SF_UNSET_SELECTOR_FIELD')),
			$setup);
      
		parent::__construct($setup);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	protected function getElements()
	{
		$fieldInfo = $this->fieldInfo;
      
		if (empty($data))
		{
			$elems = array($this->buildLabel($field));

			if ($this->requiredIndication)
			{
				$elems[] = $this->buildRequiredIndication(
					$this->foreignSelector['Required']);
			}

			$elems[] = array(
				'span',
				array('class' => 'Empty_Foreign_Data'),
				$this->translator->get('No_Foreign_Table_Data'));

			return $elems;
		}

		$optionElements = array();
      
		$data = array_merge(
			$this->foreignSelector['Prepend_Data'],
			$data,
			$this->foreignSelector['Append_Data']);
      
		foreach ($data as $forKeyData)
		{
			$attribs = array(
				'value' => $forKeyData[
					$this->selectedFields['Field']]);
			$options = array(
				'Text' => $forKeyData[
					$this->selectedFields['Selector_Field']]);
	 
			if (isset($this->fieldValues[$field]) &&
			    ($this->fieldValues[$field] ===
			     $forKeyData[
				     $this->selectedFields['Field']]))
			{
				$attribs = array_merge($attribs, array('selected' => 'selected'));
			}
	 
			$optionElements[] = array('option', $attribs, $options);
		}
      
		if (isset($this->fieldAttribs[$field]))
		{
			$attribArr = $this->fieldAttribs[$field];
		}
		else
		{
			$attribArr = array();
		}
      
		if (isset($this->highlightedFields[$field]))
		{
			if (isset($attribArr['class']))
			{
				$attribArr['class'] .= ' Highlighted';
			}
			else
			{
				$attribArr['class'] = 'Highlighted';
			}
		}
      
		return array(
			$this->buildLabel($field),
			$this->buildRequiredIndication(
				$this->foreignSelector['Required']),
			array('select',
			      array_merge($attribArr, array('name' => $field)),
			      $optionElements));
	}
}
// EOF