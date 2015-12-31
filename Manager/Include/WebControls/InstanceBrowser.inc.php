<?php
	namespace Objectify\WebControls;

	use Phast\WebControlAttribute;
	
	use Phast\WebControls\TextBox;
	use Phast\WebControls\TextBoxItem;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
			
	class InstanceBrowser extends TextBox
	{
		/**
		 * The TenantObjects allowed to be selected from this InstanceBrowser.
		 * @var TenantObject[]
		 */
		public $ValidObjects;
		
		/**
		 * A comma-separated list of object names used in markup to populate the ValidObjects property.
		 * @var string
		 */
		public $ValidObjectNames;
		
		/**
		 * The TenantObjectInstances that are selected.
		 * @var TenantObjectInstance[]
		 */
		public $SelectedInstances;

		/**
		 * Determines if more than one TenantObjectInstance may be selected from this InstanceBrowser.
		 * @var boolean
		 */
		public $MultiSelect;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ClassList[] = "InstanceBrowser";
			$this->SelectedInstances = array();
			$this->ValidObjects = array();
		}
		
		protected function RenderBeginTag()
		{
			// define the valid objects
			$attval = "";
			
			$validObjectNames = explode(",", $this->ValidObjectNames);
			$count = count($validObjectNames);
			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					$attval .= TenantObject::GetByName($validObjectNames[$i])->ID;
					if ($i < $count - 1) $attval .= ",";
				}
			}
			
			$count = count($this->ValidObjects);
			if ($count > 0)
			{
				if ($attval != "") $attval .= ",";
				for ($i = 0; $i < $count; $i++)
				{
					$attval .= $this->ValidObjects[$i]->ID;
					if ($i < $count - 1) $attval .= ",";
				}
			}
			
			if ($attval != "") $this->Attributes[] = new WebControlAttribute("data-valid-objects", $attval);
				
			// define the selected instances
			$attval = "";
			$count = count($this->SelectedInstances);
			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					$attval .= $this->SelectedInstances[$i]->ID;
					if ($i < $count - 1) $attval .= ",";
				}
				$this->Attributes[] = new WebControlAttribute("data-selected-instances", $attval);
			}
			
			parent::RenderBeginTag();
		}
	}
?>