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
	
	$objTenantType = TenantObject::Create("TenantType", $objObject);
	$objTenantType->CreateInstanceProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	$objTenantType->CreateInstanceProperty("Description", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));


	$instTenantTypeProduction_Title_English = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "Production")
	));
	$instTenantTypeProduction_Description_English = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "The production tenant type. Usually the only tenant type visible to regular users. Add more (such as production, development, sandbox, sandbox preview, implementation preview, implementation) as needed.")
	));
	
	$instTenantTypeProduction = $objTenantType->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Title"), new MultipleInstanceProperty(array($instTenantTypeProduction_Title_English), array($objLanguageString))),
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Description"), new MultipleInstanceProperty(array($instTenantTypeProduction_Description_English), array($objLanguageString)))
	));
	

	$instTenantTypeDevelopment_Title_English = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "Development")
	));
	$instTenantTypeDevelopment_Description_English = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "The development tenant type. Usually used to create new features and modules before pushing them to production. Add more (such as production, development, sandbox, sandbox preview, implementation preview, implementation) as needed.")
	));
	
	$instTenantTypeDevelopment = $objTenantType->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Title"), new MultipleInstanceProperty(array($instTenantTypeDevelopment_Title_English), array($objLanguageString))),
		new TenantObjectInstancePropertyValue($objTenantType->GetInstanceProperty("Description"), new MultipleInstanceProperty(array($instTenantTypeDevelopment_Description_English), array($objLanguageString)))
	));
?>