<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\TextBox;
	use Phast\WebControls\TextBoxItem;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	
	class ObjectBrowser extends TextBox
	{
		public function __construct()
		{
			parent::__construct();
			
			$objects = TenantObject::Get();
			
			foreach ($objects as $obj)
			{
				$this->Items[] = new TextBoxItem($obj->GetTitle(), $obj->ID);
			}
		}
	}
?>