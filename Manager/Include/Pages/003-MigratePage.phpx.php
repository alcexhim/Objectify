<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
use Objectify\Objects\Relationship;
use Objectify\Objects\Instance;
			
	class MigratePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$sourceTenant = Tenant::GetCurrent();
				$destinationTenantName = $_POST["migration_Destination"];
				
				$destinationTenant = Tenant::Create($destinationTenantName);
				if ($destinationTenant === false)
				{
					return;
				}
				
				$objects = $sourceTenant->GetObjects();
				foreach ($objects as $obj)
				{
					$objDest = TenantObject::Create($obj->Name, null, $obj->GlobalIdentifier, $destinationTenant);
					
					if ($obj->ID == 1) continue;
					
					$atts = $objDest->GetAttributes();
					$insts = $obj->GetInstances(null, false);
					foreach ($insts as $instOld)
					{
						$instNew = $objDest->CreateInstance(null, $instOld->GlobalIdentifier, $destinationTenant);
						foreach ($atts as $att)
						{
							$instNew->SetAttributeValue($att, $instOld->GetAttributeValue($att));
						}
					}
				}
				
				$rels = Relationship::Get($sourceTenant);
				foreach ($rels as $rel)
				{
					$relationshipInstance = Instance::GetByID($rel->RelationshipInstance->ID, $rel->RelationshipInstance->ParentObject->ID, $destinationTenant);
					$sourceInstance = Instance::GetByID($rel->SourceInstance->ID, $rel->SourceInstance->ParentObject->ID, $destinationTenant);
					$destinationInstances = array();
					$destInsts = $rel->GetDestinationInstances();
					foreach ($destInsts as $destInst)
					{
						$destinationInstances[] = Instance::GetByID($destInst->ID, $destInst->ParentObject->ID, $destinationTenant);
					}
					
					Relationship::Create($relationshipInstance, $sourceInstance, $destinationInstances, $destinationTenant);
				}
			}
		}
	}
?>