<?php
	namespace Objectify\WebControls;

	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\System;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\Button;
	use Phast\WebControls\MenuItemCommand;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\TenantObjectInstance;
	
	class TaskButton extends Button
	{
		public $Instance;
		public $InstanceID;
		public $InstanceGlobalIdentifier;
		
		public function __construct()
		{
			parent::__construct();
			$this->ClassList[] = "ofx-TaskButton";
		}
		
		protected function RenderBeginTag()
		{
			if ($this->Instance != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-instance-id", $this->Instance->ID);
			}
			else if ($this->InstanceID != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-instance-id", $this->InstanceID);
			}
			else if ($this->InstanceGlobalIdentifier != null)
			{
				$inst = TenantObjectInstance::GetByGlobalIdentifier($this->InstanceGlobalIdentifier);
				$this->Attributes[] = new WebControlAttribute("data-instance-id", $inst->ID);
			}
			parent::RenderBeginTag();
		}
	}
?>