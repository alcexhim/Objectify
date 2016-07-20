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
		public $ObjectName;
		
		public $ShowURL;
		public $ShowText;
		
		public function __construct($object)
		{
			parent::__construct();
			
			$this->CurrentObject = $object;
			$this->TagName = "div";
			$this->ClassList[] = "ObjectDisplayWidget";
			
			$this->ShowURL = true;
			$this->ShowText = true;
		}
		
		protected function RenderBeginTag()
		{
			if ($this->CurrentObject == null)
			{
				if (is_numeric($this->ObjectID))
				{
					$this->CurrentObject = TenantObject::GetByID($this->ObjectID);
				}
				else if ($this->ObjectName != null)
				{
					$this->CurrentObject = TenantObject::GetByName($this->ObjectName);
				}
			}
			
			if ($this->CurrentObject != null)
			{
				$adw = new AdditionalDetailWidget();
				
				$adw->TargetURL = "~/objects/modify/" . $this->CurrentObject->ID;
				$adw->Text = $this->CurrentObject->ToString();
				
				$adw->ClassTitle = "Object";
				$adw->ShowText = $this->ShowText;
				$adw->ShowURL = $this->ShowURL;
				
				/*
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
						$taskName = $inst->ToString();
						$adw->MenuItems[] = new MenuItemCommand
						(
							$taskName
						);
					}
				}
				*/
				
				$this->Controls[] = $adw;
			}
			parent::RenderBeginTag();
		}
	}

?>