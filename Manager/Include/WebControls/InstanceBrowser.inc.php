<?php
	namespace Objectify\WebControls;
	
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
		public $ObjectTypes;
		
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
			
			foreach ($this->ObjectTypes as $obj)
			{
				$insts = $obj->GetInstances();
				foreach ($insts as $inst)
				{
					$this->Items[] = new TextBoxItem($inst->ToString(), $inst->ID);
				}
			}
		}
	}
?>