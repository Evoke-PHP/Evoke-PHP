<?php
namespace Evoke\Model\DB\Joint;

class Menu
{
	public function __construct(Iface\Provider $provider)
	{
		$provider->make('Evoke\DB\Table\Joins',
		                array('Joins'      => $provider->make('Evoke\DB\Table\Joins',
		                                                      array('Joins'        => array(),
		                                                            'Parent_Field' => 'List_ID',
		                                                            'Child_Field'  => 'Menu_ID',
		                                                            'Table_Name'   => 'Menu_List'),
		                      'Table_Name' => 'Menu')
		
		parent::__construct(
	
		$provider->define($this->namespace['Model'] . 'DB\Joint',
		                  array('tableName' => 'Menu',
		                        'Joins'     => 'Evoke\DB\Table\Joins'));
			
		return $provider->make($this->namespace['Model'] . 'DB\Joint');

		return $this->build(
			$this->namespace['Model'] . 'DB\Joint',
			
		
		$setup = array(
			'Event_Manager'  => $this->getEventManager(),
			'Failures'      => $this->buildMessageTree(),
			'Joins'         => $this->getJoins(
				array('Joins' => array(
					      'List_ID' => array('Child_Field' => 'Menu_ID',
					                         'Table_Name'  => 'Menu_List')),
				      'Table_Name' => 'Menu')),
			'Notifications' => $this->buildMessageTree(),
			'Select'        => array(
				'Conditions' => array('Menu.Name' => $menuName),
				'Fields'     => '*',
				'Order'      => 'Lft ASC',
				'Limit'      => 0),
			'SQL'           => $this->getSQL(),
			'Table_Name'    => 'Menu');			

		return $this->build($this->namespace['Model'] . 'DB\Joint', $setup);

}