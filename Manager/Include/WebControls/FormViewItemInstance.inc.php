<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\FormViewItem;
	use Objectify\Objects\TenantObject;
	
	class FormViewItemInstance extends FormViewItem
	{
		/**
		 * The TenantObjects whose instances are allowed to be selected from this InstanceBrowser. 
		 * @var TenantObject[]
		 */
		public $ValidObjects;
		
		/**
		 * A comma-separated list of object names used in markup to populate the ValidObjects property.
		 * @var string
		 */
		public $ValidObjectNames;
		
		/**
		 * Determines if more than one TenantObjectInstance may be selected from this InstanceBrowser.
		 * @var boolean
		 */
		public $MultiSelect;
		
		protected function CreateControlInternal()
		{
			$elem = new InstanceBrowser();
			$elem->ValidObjectNames = $this->ValidObjectNames;
			$elem->ValidObjects = $this->ValidObjects;
			$elem->MultiSelect = $this->MultiSelect;
			
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			$elem->InnerHTML = $this->DefaultValue;
			if (isset($this->Value)) $elem->InnerHTML = System::ExpandRelativePath($this->Value);
			
			return $elem;
		}
	}
?>