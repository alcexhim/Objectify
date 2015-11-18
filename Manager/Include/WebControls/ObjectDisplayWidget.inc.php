<?php
	namespace Objectify\WebControls;

	use Phast\WebControl;
	use Phast\System;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\MenuItemCommand;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\TenantObjectInstance;
				
	class ObjectDisplayWidget extends WebControl
	{
		public $CurrentObject;
		
		public $ObjectID;
		
		public function __construct($object)
		{
			parent::__construct();
			
			$this->CurrentObject = $object;
			$this->TagName = "span";
		}
		
		protected function RenderBeginTag()
		{
			if ($this->CurrentObject == null)
			{
				$this->CurrentObject = TenantObject::GetByID($this->ObjectID);
			}
			$adw = new AdditionalDetailWidget();
			$adw->TargetURL = "~/objects/modify/" . $this->CurrentObject->ID;
			$adw->Text = $this->CurrentObject->Name;
			$adw->ClassTitle = "Object";
			
			$propTasks = $this->CurrentObject->GetPropertyValue("Tasks");
			if ($propTasks != null)
			{
				$objTask = TenantObject::GetByName("Task");
				
				$insts = $propTasks->GetInstances();
				foreach ($insts as $inst)
				{
					// TODO: determine if the Task is a
					//		'Client-Side Script Task',
					//		'Web Page Navigation Task',
					//	or	'XquizIT Script Builder Task'
					
					// also check Task instance Security Groups, etc...
					$taskName = $inst->GetPropertyValue($objTask->GetInstanceProperty("Name"));
					$adw->MenuItems[] = new MenuItemCommand
					(
						$taskName
					);
				}
			}
			
			$this->Controls[] = $adw;
			
			parent::RenderBeginTag();
		}
	}

?>