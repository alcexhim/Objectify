<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\Relationship;
	
	use Objectify\Objects\Objectify;
	
	use Phast\Utilities\Stopwatch;
use Objectify\Objects\TenantObjectInstance;
				
	class TestingPage extends PhastPage
	{
		private function RecursivePrintParentObjects($po, $level = 0)
		{
			for ($i = 0; $i < $level; $i++)
			{
				echo ("\t");
			}
			echo ($po->Name . "\r\n");
			$pos = $po->GetParentObjects();
			foreach ($pos as $ppo)
			{
				$this->RecursivePrintParentObjects($ppo, $level + 1);
			}
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			header("Content-Type: text/plain");
			

			$po = TenantObject::GetByName("TextAttribute");
			
			$this->RecursivePrintParentObjects($po);
			
			echo ("\r\n\r\n");
			
			$poinst = TenantObjectInstance::GetByGlobalIdentifier($po->GlobalIdentifier);
			
			$rels = Relationship::GetBySourceInstance($poinst);
			foreach ($rels as $rel)
			{
				echo ($rel->RelationshipInstance->ToString() . "\r\n");
			}
			echo("\r\n\r\n");
			die();
			
			/*
			// test getting relationships for classes
			$objRelationship = TenantObject::GetByName("Relationship");
			
			// get the `Class.has Attribute` relationship
			$objClass = TenantObject::GetByName("Class");
			$objAttribute = TenantObject::GetByName("Attribute");
			
			$instRelationship_Class__has_Attribute = $objRelationship->GetInstance(array
			(
				new TenantObjectInstancePropertyValue("SourceObject", $objClass),
				new TenantObjectInstancePropertyValue("RelationshipType", "has"),
				new TenantObjectInstancePropertyValue("DestinationObject", $objAttribute)
			));
			
			// get all relationship entries for this relationship
			$objRelationshipEntry = TenantObject::GetByName("RelationshipEntry");
			$insts = $objRelationshipEntry->GetInstances(array
			(
				new TenantObjectInstancePropertyValue("Relationship", $instRelationship_Class__has_Attribute)
			));
			
			// if we also filter on class, we can get all attributes on a particular Class, etc. etc. PROFIT!!!
			print_r($insts);
			*/
			
			/*
			$rels = Relationship::Get();
			print_r($rels);
			*/
			
			// test attributes
			$objClass = TenantObject::GetByName("Class");
			$instClass = TenantObjectInstance::GetByGlobalIdentifier($objClass->GlobalIdentifier);
			
			$instAttribute_Name = TenantObjectInstance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
			
			$instClass->SetAttributeValue($instAttribute_Name, "Class");
			
			return;
			
			
			$sw = new Stopwatch();
			
			$objMethod = TenantObject::GetByName("Method");
			$instMethod = $objMethod->GetInstance(array
			(
				new TenantObjectInstancePropertyValue("Name", "GetLoginTokenForCurrentUser")
			));
			
			echo ("Method name: " . $instMethod->ToString());
			echo ("\r\n");
			
			$sw->start();
			$result = Objectify::ExecuteMethod("GetLoginTokenForCurrentUser");
			$sw->stop();
			
			if ($result == null)
			{
				echo ("Method Return Value is null");
			}
			else
			{
				echo ("Method Return Value: " . $result);
			}
			echo ("\r\n");
			echo ("Method Time: " . $sw->getElapsedTime());
			
			echo ("\r\n");
			echo ("\r\n");
			
			$sw->start();
			$result = $_SESSION["Authentication.LoginToken"];
			$sw->stop();
			
			echo ("Non-Method Expression: \$_SESSION[\"Authentication.LoginToken\"]");
			echo ("\r\n");
			
			echo ("Non-Method Result: " . $result);
			echo ("\r\n");
			echo ("Non-Method Time: " . $sw->getElapsedTime());
			
			die();
		}
	}
?>