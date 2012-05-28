<?php
namespace Evoke\View\Admin;

class Header extends \Evoke\View\Admin
{
	/** @property $viewLanguage
	 *  Language view
	 */
	protected $viewLanguage;

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
		$setup += array('View_Language' => NULL,
		                'Languages'        => NULL,
		                'Translator'       => NULL);

		if (!$viewLanguage instanceof \Evoke\Core\Iface\View)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires View_Language');
		}

		if (!$translator instanceof \Evoke\Core\Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		parent::__construct($setup);

		$this->viewLanguage = $viewLanguage;
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
			            $this->viewLanguages->set($data))));
	}
}
// EOF