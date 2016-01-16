<?php
	namespace Objectify\WebControls;

	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\System;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\MenuItemCommand;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\TenantObjectInstance;
	
	class InstanceDisplayWidget extends WebControl
	{
		public $CurrentInstance;
		public $InstanceID;
		
		public function __construct($instance)
		{
			parent::__construct();
			
			$this->CurrentInstance = $instance;
			$this->TagName = "div";
			$this->ClassList[] = "InstanceDisplayWidget";
		}
		
		protected function RenderBeginTag()
		{
			if ($this->CurrentInstance == null)
			{
				$this->CurrentInstance = TenantObjectInstance::GetByID($this->InstanceID);
			}
			
			if ($this->CurrentInstance != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-instance-id", $this->CurrentInstance->ID);
				
				$adw = new AdditionalDetailWidget();
				$iid = $this->CurrentInstance->GetInstanceID();
				$iid = str_replace('$', '\\$', $iid);
				$adw->TargetURL = "~/instances/modify/" . $iid;
				$adw->Text = $this->CurrentInstance->ToString();
				$adw->ClassTitle = $this->CurrentInstance->ParentObject->ToString();
				
				/*
				$propTasks = $this->CurrentInstance->ParentObject->GetPropertyValue("Tasks");
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