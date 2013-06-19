<?php
/**
 * Record List View.
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\RecordListIface,
	InvalidArgumentException;

/**
 * Record List View
 *
 * View to represent a list of records.
 *
 * Regex notation (whitespace should be ignored).
 *
 * Heading Row = <Field View>*
 * Record      = (<Field View><Value View>)* | <Value View>*
 * Record Row  = <Record><Buttons View>
 *
 * Record List = <Heading Row>?
 *               (<Empty View> | (<Record Row>+<Heading Row>?)+)
 *               <Heading Row>?
 *
 * In english: optional headings at top, followed by empty view or one or more
 * records optionally separated by headings, followed by optional headings at
 * the bottom.
 *
 * This is a composite view which controls the above layout using the Heading
 * Options and the views passed into the constructor.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class RecordList extends View
{
	protected
		/**
		 * The fields to display (empty for none, NULL for all).
		 * @var string[]|null
		 */
		$displayedFields,
		
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
		 * Field View.
		 * @var ViewIface
		 */
		$viewField,

		/**
		 * Value view.
		 * @var ViewIface
		 */
		$viewValue;

	/**
	 * Construct a RecordList View.
	 *
	 * @param ViewIface[] Array of views for Buttons, Empty, Field and Value.
	 */
	public function __construct(Array $views)
	{
		if (!$views['Buttons'] instanceof ViewIface ||
		    !$views['Empty'] instanceof ViewIface ||
		    !$views['Field'] instanceof ViewIface ||
		    !$views['Value'] instanceof ViewIface)
		{
			throw new InvalidArgumentException(
				'needs views: Buttons, Empty, Field and Value has: ' .
				var_export($views, true));
		}
			
		$this->headingOptions = array('Bottom'    => false,
		                              'Inline'    => false,
		                              'Separator' => -1,
		                              'Top'       => true);
		$this->viewButtons    = $views['Buttons'];
		$this->viewEmpty      = $views['Empty'];
		$this->viewField   	  = $views['Field'];
		$this->viewValue      = $views['Value'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the record list.
	 *
	 * @return mixed[] The record list view.
	 */
	public function get()
	{
		$recordListElems = array();
		$row = 0;

		// Calculate the headings for use throughout the record list.
		$headings = array();

		foreach ($this->displayedFields as $field)
		{
			$this->viewField->setParam('Field', $field);
			$headings[$field] = $this->viewField->get();
		}
		
		// Compose the view using the components.
		if ($this->headingOptions['Top'])
		{
			$recordListElems[] = array(
				'div', array('class' => 'Headings Top'), $headings);
		}

		if (empty($this->data))
		{
			$recordListElems[] = $this->viewEmpty->get();
		}
		else
		{
			foreach ($this->data as $record)
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
				
				foreach ($this->displayedFields as $field)
				{
					$this->viewValue->setParam('Field', $field);
					$this->viewValue->setParam('Value', $this->data[$field]);
					
					$recordElems[] = $this->headingOptions['Inline'] ?
						array('div',
						      array('class' => 'Row'),
						      array($headings[$field],
						            $this->viewValue->get())) :
						$this->viewValue->get();
				}

				$recordSelected = $this->data->isSelectedRecord();
				$entryClass = 'Entry';

				if ($recordSelected)
				{
					$entryClass .= ' Selected';
				}

				$entryClass .= ($row % 2) ? ' Odd' : ' Even';

				$this->viewButtons->setData($record);
				$this->viewButtons->setParam('Row', $row);
				$this->viewButtons->setParam('Selected', $recordSelected);
				
				$recordListElems[] = array(
					'div',
					array('class' => $entryClass),
					array(array('div',
					            array('class' => 'Record'),
					            $recordElems),
					      $this->viewButtons->get()));
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

	/**
	 * Set the displayed fields in the record list.
	 *
	 * @param string[]|null An array of fields to display or null for all.
	 *                      An empty array means not to display any fields.
	 */
	public function setDisplayedFields(/* Mixed */ $displayedFields = NULL)
	{
		$this->displayedFields = $displayedFields;
	}

	/**
	 * Set whether a heading will appear at the bottom of the record list.
	 *
	 * @param bool Whether the bottom heading should be displayed.
	 */
	public function setHeadingBottom(/* Bool */ $display = true)
	{
		$this->headingOptions['Bottom'] = $display;
	}

	/**
	 * Set whether a headings will appear inline for the record list.
	 *
	 * @param bool Whether the inline headings should be displayed.
	 */
	public function setHeadingInline(/* Bool */ $display = true)
	{
		$this->headingOptions['Inline'] = $display;
	}

	/**
	 * Set the number of rows between heading separators.
	 *
	 * @param int The number of rows between heading separators.
	 */
	public function setHeadingSeparatorRows(/* Int */ $separation)
	{
		$this->headingOptions['Separator'] = $separation;
	}
	
	/**
	 * Set whether a heading will appear at the top of the record list.
	 *
	 * @param bool Whether the top heading should be displayed.
	 */
	public function setHeadingTop(/* Bool */ $display = true)
	{
		$this->headingOptions['Top'] = $display;
	}
}
// EOF