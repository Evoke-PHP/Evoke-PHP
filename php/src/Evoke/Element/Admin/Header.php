<?php
namespace Evoke\Element\Admin;

class Header extends \Evoke\Element\Base
{
	/** @property $ElementLanguage
	 *  Language element
	 */
	protected $ElementLanguage;

	/** @property $languages
	 *  \array of languages.
	 */
	protected $languages;

	/** @property $Translator
	 *  Translator object
	 */
	protected $Translator;
	
	public function __construct(Array $setup)
	{
		$setup += array('Element_Language' => NULL,
		                'Languages'        => NULL,
		                'Translator'       => NULL);

		if (!$elementLanguage instanceof \Evoke\Core\Iface\Element)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Element_Language');
		}

		if (!$translator instanceof \Evoke\Core\Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		parent::__construct($setup);

		$this->ElementLanguage = $elementLanguage;
		$this->languages       = $languages;
		$this->Translator      = $translator;
	}
	
   /******************/
   /* Public Methods */
   /******************/

	public function set(Array $data)
	{
		return parent::set(
			array('div',
			      array('class' => 'Admin_Header'),
			      array(array('a',
			                  array('class' => 'Admin_Home',
			                        'href' => '/admin/index.php?' .
			                        $this->Translator->getLanguageHTTPQuery()),
			                  array(array(
				                        'img',
				                        array('src' => '/images/admin_home.png',
				                              'alt' => 'Home')),
			                        array('span',
			                              array(),
			                              $this->Translator->get(
				                              'Admin_Home')))),
			            $this->ElementLanguages->set($data))));
	}
}
// EOF