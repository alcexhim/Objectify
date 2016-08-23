<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\Objectify;
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
				new ListViewColumn("lvcID", "ID"),
				new ListViewColumn("lvcInstance", "Instance")
			);
			
			$props = $this->Object->GetAttributes();
			
			if ($this->AutoGenerateColumns)
			{
				foreach ($props as $prop)
				{
					$this->Columns[] = new ListViewColumn("lvcProperty" . $prop->ID, $prop->ToString());
				}
			}
			
			$insts = $this->Object->GetInstances();
			foreach ($insts as $inst)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $inst->ID),
					new ListViewItemColumn("lvcInstance", function($col)
					{
						$idw = new InstanceDisplayWidget($col->ExtraData);
						$idw->Render();
					}, $inst->ToString(), $inst)
				));
				
				foreach ($props as $prop)
				{
					$lvi->Columns[] = new ListViewItemColumn("lvcProperty" . $prop->ID, function($col)
					{
						$propval = $col->ExtraData["inst"]->GetAttributeValue($col->ExtraData["prop"]);
						echo(Objectify::HTML_FormatValue($propval));
					}, $inst->GetAttributeValue($prop), array("inst" => $inst, "prop" => $prop));
				} 
				$this->Items[] = $lvi; 
			}
			
			parent::RenderContent();
		}
	}
?>