<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\FormViewItem;
	use Objectify\Objects\TenantObject;
	
	class FormViewItemObject extends FormViewItem
	{
		/**
		 * Determines if more than one TenantObject may be selected from this ObjectBrowser.
		 * @var boolean
		 */
		public $MultiSelect;
		
		protected function CreateControlInternal()
		{
			$elem = new ObjectBrowser();
			$elem->MultiSelect = $this->MultiSelect;
			
			$elem->ID = $this->ID;
			$elem->Name = $this->Name;
			$elem->InnerHTML = $this->DefaultValue;
			if (isset($this->Value)) $elem->InnerHTML = System::ExpandRelativePath($this->Value);
			
			return $elem;
		}
	}
?>