<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	
	class InstanceListView extends ListView
	{
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
			
			if ($this->ObjectID != null)
			{
				$this->Object = TenantObject::GetByID($this->ObjectID);
			}
			if ($this->Object == null) return;
			
			$this->Columns = array
			(
				new ListViewColumn("lvcID", "ID")
			);
			
			$insts = $this->Object->GetInstances();
			foreach ($insts as $inst)
			{
				$this->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $inst->ID)
				));
			}
		}
	}
?>