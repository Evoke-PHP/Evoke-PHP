<?php
/**
 * Record List View.
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\RecordListIface,
	Evoke\View\ViewIface,
	InvalidArgumentException;

/**
 * Record List View.
 *
 * View to represent a list of records.
 *
 * +----------+
 * | *Heading |
 * |   (Top)  |
 * +----------+
 *
 * +------------+
 * | Empty View |
 * +------------+
 *      OR
 * +-------------+ +--------------+
 * | *Field View | | Buttons View |
 * +-------------+ +--------------+
 *      OR
 * +-----------------------------------+ +--------------+
 * |   +-----------------------------+ | |              |
 * | * | +----------+ +------------+ | | |              |
 * |   | | Heading  | | Field View | | | | Buttons View |
 * |   | | (Inline) | |            | | | |              |
 * |   | +----------+ +------------+ | | |              |
 * |   +-----------------------------+ | |              |
 * +-----------------------------------+ +--------------+
 *           "                                        // (Repeat)
 * +-------------+
 * |  Heading    |
 * | (Separator) |
 * +-------------+
 * +-----------------+
 * | Record as Above |
 * +-----------------+
 *       "                                            // (Repeat)
 * +----------+
 * | Headings |
 * | (Bottom) |
 * +----------+
 *
 * This is a composite view which controls the above layout using the Heading
 * Options and the views passed into the constructor.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class RecordList implements ViewIface
{
	protected
		/**
		 * The fields to display.
		 * @var string[]
		 */
		$displayFields,
		
		/**
		 * Heading Options.
		 * @var mixed[]
		 */
		$headingOptions,

		/**
		 * Buttons View.
		 * @var ViewIface
		 */
		$viewButtons,

		/**
		 * Empty View.
		 * @var ViewIface
		 */
		$viewEmpty,

		/**
		 * Field view.
		 * @var ViewIface
		 */
		$viewField,
	
		/**
		 * Heading View.
		 * @var ViewIface
		 */
		$viewHeading;

	/**
	 * Construct a RecordList View.
	 *
	 * @param string[]        The fields to display.
	 * @param ViewIface 	  Buttons View.
	 * @param ViewIface 	  Empty View.
	 * @param ViewIface 	  Record View.
	 * @param ViewIface 	  Headings View.
	 * @param mixed[]   	  Heading Options.
	 */
	public function __construct(Array           $displayFields,
	                            ViewIface 		$viewButtons,
	                            ViewIface 		$viewEmpty,
	                            ViewIface 		$viewField,
	                            ViewIface 		$viewHeading,
	                            Array     		$headingOptions = array())
	{
		$this->displayFields      = $displayFields;
		$this->headingOptions     = array_merge(array('Bottom'    => false,
		                                              'Inline'    => false,
		                                              'Separator' => -1,
		                                              'Top'       => true),
		                                        $headingOptions);
		$this->viewButtons    	  = $viewButtons;
		$this->viewEmpty      	  = $viewEmpty;
		$this->viewField          = $viewField;
		$this->viewHeading   	  = $viewHeading;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the record list.
	 *
	 * @param mixed[] Parameters for the view (Record_List).
	 * @return mixed[] The record list view.
	 */
	public function get(Array $params = array())
	{
		if (!isset($params['Record_List']))
		{
			throw new InvalidArgumentException('needs Record_List in params');
		}

		$recordList = $params['Record_List'];
		$recordListElems = array();
		$row = 0;

		// Calculate the headings for use throughout the record list.
		$headings = array();

		foreach ($this->displayFields as $field)
		{
			$headings[$field] = $this->viewHeading->get(
				array('Field' => $field));
		}
		
		// Compose the view using the components.
		if ($this->headingOptions['Top'])
		{
			$recordListElems[] = array(
				'div', array('class' => 'Headings Top'), $headings);
		}

		if ($recordList->isEmpty())
		{
			$recordListElems[] = $this->viewEmpty->get();
		}
		else
		{
			foreach ($recordList as $record)
			{				
				if ($this->headingOptions['Separator'] > 0 &&
				    (($row % $this->headingOptions['Separator']) === 0) &&
				    $row > 1)
				{
					$recordListElems[] = array(
						'div',
						array('class' => 'Headings Separator'),
						$headings);
				}
				
				$recordElems = array();
				
				if ($this->headingOptions['Inline'])
				{
					foreach ($this->displayFields as $field)
					{
						$recordElems[] = array(
							'div',
							array('class' => 'Row'),
							array($headings[$field],
							      $this->viewField->get(
								      array('Field' => $field,
								            'Value' => $record[$field]))));
					}
				}
				else
				{
					foreach ($this->displayFields as $field)
					{
						$recordElems[] = $this->viewField->get(
							array('Field'    => $field,
							      'Value'    => $record[$field]));
					}
					
				}

				$recordSelected = $recordList->isSelectedRecord();
				$entryClass = 'Entry';

				if ($recordSelected)
				{
					$entryClass .= ' Selected';
				}

				$entryClass .= ($row % 2) ? ' Odd' : ' Even';
				
				$recordListElems[] = array(
					'div',
					array('class' => $entryClass),
					array(array('div',
					            array('class' => 'Record'),
					            $recordElems),
					      $this->viewButtons->get(
						      array('Data'     => $record,
						            'Row'      => $row,
						            'Selected' => $recordSelected))));
				$row++;
			}
		}
		
		if ($this->headingOptions['Bottom'])
		{
			$recordListElems[] = array(
				'div', array('class' => 'Headings Bottom'),	$headings);
		}

		return array('div', array('class' => 'Record_List'), $recordListElems);
	}
}
// EOF