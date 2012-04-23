<?php
namespace Evoke\Element\Admin;

class Header extends \Evoke\Element\Base
{
	/** @property $elementLanguage
	 *  Language element
	 */
	protected $elementLanguage;

	/** @property $languages
	 *  \array of languages.
	 */
	protected $languages;

	/** @property $translator
	 *  Translator object
	 */
	protected $translator;
	
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

		$this->elementLanguage = $elementLanguage;
		$this->languages       = $languages;
		$this->translator      = $translator;
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
			                        $this->translator->getLanguageHTTPQuery()),
			                  array(array(
				                        'img',
				                        array('src' => '/images/admin_home.png',
				                              'alt' => 'Home')),
			                        array('span',
			                              array(),
			                              $this->translator->get(
				                              'Admin_Home')))),
			            $this->elementLanguages->set($data))));
	}
}
// EOF