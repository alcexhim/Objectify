<?php
	
	namespace Objectify\Tenant\Pages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	
	use Objectify\WebControls\InstanceDisplayWidget;
	
	class ObjectModifyPage extends PhastPage
	{
		/**
		 * The object that is being modified by this ObjectModifyPage.
		 * @var TenantObject
		 */
		public $CurrentObject;
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$this->CurrentObject = TenantObject::GetByID($e->RenderingPage->GetPathVariableValue("objectID"));
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			$tabGeneralInformation = $tbsTabs->GetTabByID("tabGeneralInformation");
			$fvGeneralInformation = $tabGeneralInformation->GetControlByID("fvGeneralInformation");
			
			if ($this->CurrentObject != null)
			{
				$fvGeneralInformation->GetItemByID("txtObjectName")->Value = $this->CurrentObject->Name;
			}
			
			$tabProperties = $tbsTabs->GetTabByID("tabProperties");
			$lvStaticProperties = $tabProperties->GetControlByID("lvStaticProperties");
			$lvInstanceProperties = $tabProperties->GetControlByID("lvInstanceProperties");
			
			$props = $this->CurrentObject->GetProperties();
			$i = 0;
			foreach ($props as $prop)
			{
				$i++;
				if ($i == 2)
				{
					$propval = $this->CurrentObject->GetPropertyValue($prop);
					$propval->SetInstance(TenantObjectInstance::GetByGlobalIdentifier("9FE564A453AE45B48110BF732164C683"));
					
					$inst = $propval->GetInstance();
					// print_r($inst);die();
				}
				
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcProperty", $prop->Name),
					new ListViewItemColumn("lvcDataType", $prop->DataType->Name),
					new ListViewItemColumn("lvcDefaultValue", function($sender)
					{
						if (is_object($sender->ExtraData))
						{
							print_r($sender->ExtraData);
							
							if (get_class($sender->ExtraData) == "Objectify\\Objects\\MultipleInstanceProperty")
							{
								$iv = new InstanceDisplayWidget();
								$iv->InstanceID = $sender->ExtraData->GetInstances()[0]->ID;
								$iv->Render();
							}
							else if (get_class($sender->ExtraData) == "Objectify\\Objects\\SingleInstanceProperty")
							{
								$iv = new InstanceDisplayWidget();
								$iv->InstanceID = $sender->ExtraData->GetInstance()->ID;
								$iv->Render();
							}
							else
							{
								print_r($sender->ExtraData);
							}
						}
						else
						{
							echo($sender->ExtraData);
						}
					}, null, $prop->DefaultValue)
				));
				
				$lvStaticProperties->Items[] = $lvi;
			}
			
			$props = $this->CurrentObject->GetInstanceProperties();
			foreach ($props as $prop)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcProperty", $prop->Name),
					new ListViewItemColumn("lvcDataType", $prop->DataType->Name),
					new ListViewItemColumn("lvcDefaultValue", $prop->DefaultValue)
				));
				$lvInstanceProperties->Items[] = $lvi;
			}
		}
	}
	
?>