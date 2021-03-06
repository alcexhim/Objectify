<?php
	namespace Objectify\WebControls;
	
	use Phast\System;
	use Phast\WebControls\FormViewItem;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	
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
		
		/**
		 * The TenantObjectInstances that are selected.
		 * @var TenantObjectInstance[]
		 */
		public $SelectedInstances;
		
		/**
		 * Creates a new Instance FormViewItem with the given parameters.
		 * @param string $id The control ID for the FormViewItem.
		 * @param string $name The name of the form field to associate with the FormViewItem.
		 * @param string $title The title of the FormViewItem.
		 * @param string $defaultValue The default value of the FormViewItem.
		 */
		public function __construct($id = null, $name = null, $title = null, $defaultValue = null)
		{
			parent::__construct($id, $name, $title, $defaultValue);
			$this->SelectedInstances = array();
		}
		
		protected function CreateControlInternal()
		{
			if ($this->ReadOnly)
			{
				$elem = new InstanceDisplayWidget();
				$elem->CurrentInstance = $this->Value;
			}
			else
			{
				$elem = new InstanceBrowser();
				$elem->ValidObjectNames = $this->ValidObjectNames;
				$elem->ValidObjects = $this->ValidObjects;
				$elem->MultiSelect = $this->MultiSelect;
				
				foreach ($this->SelectedInstances as $inst)
				{
					$elem->SelectedInstances[] = $inst;
				}
				
				$elem->ID = $this->ID;
				$elem->Name = $this->Name;
				$elem->InnerHTML = $this->DefaultValue;
				if (isset($this->Value)) $elem->InnerHTML = System::ExpandRelativePath($this->Value);
			}
			return $elem;
		}
	}
?>