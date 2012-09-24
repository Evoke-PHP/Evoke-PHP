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
 *        		       +---------------+
 *        		       | Headings View |
 *        			   +---------------+
 * +-----------------+ +---------------+ +--------------+
 * | Inline Headings | | Record View / | | Buttons View |
 * | View            | | Empty View    | |              |
 * +-----------------+ +---------------+ +--------------+
 *
 *    "         "          "        "       "        "    // (Repeat)
 *                     +---------------+
 *                     | Headings View |
 *                     |  (Separator)  |
 *                     +---------------+
 * +-----------------+ +---------------+ +--------------+
 * | Inline Headings | | Record View   | | Buttons View |
 * | View            | |               | |              |
 * +-----------------+ +---------------+ +--------------+
 *
 *    "         "          "        "       "        "    // (Repeat)
 *                     +---------------+
 *                     | Headings View |
 *                     |   (Bottom)    |
 *                     +---------------+
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
	/**
	 * Attributes for the record list.
	 * @var string[]
	 */
	protected $attribs;

	/**
	 * Attributes for a single entry within the record list.
	 * @var string[]
	 */
	protected $entryAttribs;
	
	/**
	 * Heading Options.
	 * @var mixed[]
	 */
	protected $headingOptions;
	 
	/**
	 * The record list data.
	 * @var RecordListIface
	 */
	protected $recordList;

	/**
	 * Buttons View.
	 * @var ViewIface
	 */
	protected $viewButtons;

	/**
	 * Empty View.
	 * @var ViewIface
	 */
	protected $viewEmpty;
	
	/**
	 * Headings View.
	 * @var ViewIface
	 */
	protected $viewHeadings;
	
	/**
	 * Inline Headings View.
	 * @var ViewIface
	 */
	protected $viewHeadingsInline;
	
	/**
	 * Record View.
	 * @var ViewIface
	 */
	protected $viewRecord;

	/**
	 * Construct a RecordList View.
	 *
	 * @param RecordListIface Record List data.
	 * @param ViewIface 	  Buttons View.
	 * @param ViewIface 	  Empty View.
	 * @param ViewIface 	  Record View.
	 * @param string[]        Record List Attributes.
	 * @param string[]        Entry Attributes.
	 * @param ViewIface 	  Headings View.
	 * @param ViewIface 	  Inline Headings View.
	 * @param mixed[]   	  Heading Options.
	 */
	public function __construct(RecordListIface $recordList,
	                            ViewIface 		$viewEmpty,
	                            ViewIface 		$viewRecord,
	                            Array           $attribs            = array(
		                            'class' => 'Record_List'),
	                            Array           $entryAttribs        = array(
		                            'class' => 'Entry'),
	                            ViewIface 		$viewButtons        = NULL,
	                            ViewIface 		$viewHeadings       = NULL,
	                            ViewIface 		$viewHeadingsInline = NULL,
	                            Array     		$headingOptions     = array())
	{
		$this->headingOptions = array_merge($headingOptions,
		                                    array('Bottom'    => false,
		                                          'Inline'    => false,
		                                          'Separator' => -1,
		                                          'Top'       => true));

		if (($this->headingOptions['Bottom'] ||
		     $this->headingOptions['Separator'] > 0 ||
		     $this->headingOptions['Top']) &&
		    !$viewHeadings instanceof ViewIface)
		{
			throw new InvalidArgumentException(
				'needs viewHeadings with the specified headingOptions.');
		}
		
		if ($this->headingOptions['Inline'] &&
		    !$viewHeadingsInline instanceof ViewIface)
		{
			throw new InvalidArgumentException(
				'needs viewHeadingsInline with specified headingOptions.');
		}

		$this->attribs            = $attribs;
		$this->entryAttribs       = $entryAttribs;
		$this->recordList      	  = $recordList;
		$this->viewButtons    	  = $viewButtons;
		$this->viewEmpty      	  = $viewEmpty;
		$this->viewHeadings   	  = $viewHeadings;
		$this->viewHeadingsInline = $viewHeadingsInline;
		$this->viewRecord         = $viewRecord;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the record list.
	 *
	 * @param mixed[] The are no parameters for the record list so this is
	 *                ignored.
	 *
	 * @return mixed[] The record list view.
	 */
	public function get(Array $params = array())
	{
		$recordListElems = array();
		$row = 0;

		// Calculate the heading elements once as they may be repeated
		// throughout.
		if ($this->viewHeadings instanceof ViewIface)
		{
			$heading = $this->viewHeadings->get();
		}

		if ($this->viewHeadingsInline instanceof ViewIface)
		{
			$headingInlineSelected =
				$this->viewHeadingsInline->get(array('Selected' => true));
			$headingInlineDeselected =
				$this->viewHeadingsInline->get(array('Selected' => false));
		}

		// Compose the view using the components.
		if ($this->headingOptions['Top'])
		{
			$recordListElems[] = $heading;
		}

		if ($this->recordList->isEmpty())
		{
			$recordListElems[] = $this->viewEmpty->get();
		}
		else
		{
			foreach ($this->recordList as $record)
			{
				$entryElems = array();
				$recordSelected = $this->recordList->isSelectedRecord();
				
				if ($this->headingOptions['Separator'] > 0 &&
				    (($row % $this->headingOptions['Separator']) === 0) &&
				    $row > 1)
				{
					$recordListElems[] = $heading;
				}
				
				if ($this->headingOptions['Inline'])
				{
					$entryElems[] = $recordSelected ?
						$headingInlineSelected : $headingInlineDeselected;
				}
				
				$entryElems[] = $this->viewRecord->get(
					array('Data'     => $record,
					      'Row'      => $row,
					      'Selected' => $recordSelected));

				if (isset($this->viewButtons))
				{
					$entryElems[] = $this->viewButtons->get(
						array('Data'     => $record,
						      'Row'      => $row,
						      'Selected' => $recordSelected));
				}
				
				$recordListElems[] = array(
					'div', $this->entryAttribs, $entryElems);
				$row++;
			}
		}
		
		if ($this->headingOptions['Bottom'])
		{
			$recordListElems[] = $heading;
		}

		return array('div', $this->attribs, $recordListElems);
	}
}
// EOF