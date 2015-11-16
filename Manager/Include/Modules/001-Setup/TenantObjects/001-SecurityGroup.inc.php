<?php

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	use Objectify\Objects\DataType;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\TenantObjectMethodParameter;
	use Objectify\Objects\SingleInstanceProperty;
	use Objectify\Objects\MultipleInstanceProperty;
	
	$objSecurityPermission = TenantObject::Create("SecurityPermission");
	$objSecurityPermission->CreateInstanceProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	
	// Create the language string entry for ProvisionTenants - English ("Provision Tenants")
	$instLanguageString_English_ProvisionTenants = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "Provision Tenants")
	));
	
	// Create the "Provision Tenants" security permission instance
	$instSecurityPermission_ProvisionTenants = $objSecurityPermission->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objSecurityPermission->GetInstanceProperty("Title"), new MultipleInstanceProperty
		(
			array($instLanguageString_English_ProvisionTenants),
			array($objLanguageString)
		))
	));
	
	$instSecurityGroup_SystemAdministrator_Title = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "System Administrator")
	));
	
	$objSecurityGroup = TenantObject::Create("SecurityGroup");
	$objSecurityGroup->CreateInstanceProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	$objSecurityGroup->CreateInstanceProperty("Permissions", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objSecurityPermission)));
	
	$instSecurityGroup_SystemAdministrator = $objSecurityGroup->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objSecurityGroup->GetInstanceProperty("Title"), new MultipleInstanceProperty(array
		(
			$instSecurityGroup_SystemAdministrator_Title
		),
		array
		(
			$objLanguageString
		))),
		new TenantObjectInstancePropertyValue($objSecurityGroup->GetInstanceProperty("Permissions"), new MultipleInstanceProperty(array
		(
			$instSecurityPermission_ProvisionTenants
		),
		array
		(
			$objSecurityPermission
		)))
	));
?>