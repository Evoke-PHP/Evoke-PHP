<?php
/**
 * NotFound
 *
 * @package View
 */
namespace Evoke\View;

use Evoke\Model\Data\TranslationsIface;

/**
 * NotFound
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class NotFound implements ViewIface
{
	/**
	 * Translations
	 * @var TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a NotFound object.
	 *
	 * @param TranslationsIface Translations.
	 */
	public function __construct(TranslationsIface $translations)
	{
		$this->translations = $translations;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */	
	public function get(Array $params = array())
	{
		$messageBoxElements = array(
			array('div',
			      array('class' => 'Title'),
			      $this->translations->tr('Not_Found_Title')));

		if (isset($params['Image_Element']))
		{
			$messageBoxElements[] = $params['Image_Element'];
		}

		$messageBoxElements[] =
			array('div',
			      array('class' => 'Description'),
			      $this->translations->tr('Not_Found_Description'));
		
		return array('div',
		             array('class' => 'Not_Found Message_Box System'),
		             $messageBoxElements);
	}
}
// EOF
