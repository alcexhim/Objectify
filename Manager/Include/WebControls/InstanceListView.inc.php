<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	use Phast\System;
	
	class InstanceListView extends ListView
	{
		public $AutoGenerateColumns;
		
		/**
		 * The ID of the TenantObject for which to view instances.
		 * @var int
		 */
		public $ObjectID;
		/**
		 * The TenantObject for which to view instances.
		 * @var TenantObject
		 */
		public $Object;
		
		public function __construct()
		{
			parent::__construct();
			$this->AutoGenerateColumns = true;
		}
		
		protected function RenderContent()
		{
			if ($this->ObjectID != null)
			{
				$this->Object = TenantObject::GetByID(System::ExpandRelativePath($this->ObjectID));
			}
			if ($this->Object == null) return;
			
			$this->Columns = array
			(
				new ListViewColumn("lvcID", "ID")
			);
			
			$props = $this->Object->GetInstanceProperties();
			
			if ($this->AutoGenerateColumns)
			{
				foreach ($props as $prop)
				{
					$this->Columns[] = new ListViewColumn("lvcProperty" . $prop->ID, $prop->Name);
				}
			}
			
			$insts = $this->Object->GetInstances();
			foreach ($insts as $inst)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $inst->ID)
				));
				
				foreach ($props as $prop)
				{
					$lvi->Columns[] = new ListViewItemColumn("lvcProperty" . $prop->ID, function($col)
					{
						$propval = $col->ExtraData["inst"]->GetPropertyValue($col->ExtraData["prop"]);
						if (is_object($propval))
						{
							if (get_class($propval) == "Objectify\\Objects\\SingleInstanceProperty")
							{
								$inst = $propval->GetInstance();
								$odw = new InstanceDisplayWidget($inst);
								$odw->Render();
							}
							else if (get_class($propval) == "Objectify\\Objects\\MultipleInstanceProperty")
							{
								$insts = $propval->GetInstances();
								foreach ($insts as $inst)
								{
									$odw = new InstanceDisplayWidget($inst);
									$odw->Render();
									echo("<br />");
								}
							}
						}
						else
						{
							echo($propval);
						}
					}, $inst->GetPropertyValue($prop), array("inst" => $inst, "prop" => $prop));
				} 
				$this->Items[] = $lvi; 
			}
			
			parent::RenderContent();
		}
	}
?>