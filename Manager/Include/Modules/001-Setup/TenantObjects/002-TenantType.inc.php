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
	use Objectify\Objects\MultipleInstanceProperty;
	
	$objTenantType = TenantObject::Create("TenantType");
	
	$instTenantTypeProduction = $objTenantType->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Title"), "Production"),
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Description"), "The production tenant type. Usually the only tenant type visible to regular users. Add more (such as production, development, sandbox, sandbox preview, implementation preview, implementation) as needed.")
	));
	$instTenantTypeDevelopment = $objTenantType->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Title"), "Development"),
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Description"), "The development tenant type. Usually used to create new features and modules before pushing them to production. Add more (such as production, development, sandbox, sandbox preview, implementation preview, implementation) as needed.")
	));
?>