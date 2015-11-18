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

	$objTask = TenantObject::Create("Task", $objObject);
	$objTask->CreateInstanceProperty("Name", DataType::GetByName("Text"));
	$objTask->CreateInstanceProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	
	$instTask_Object_Delete_Title_English = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "Delete")
	));
	$instTask_Object_Delete = $objTask->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objTask->GetInstanceProperty("Name"), "Delete"),
		new TenantObjectInstancePropertyValue($objTask->GetInstanceProperty("Title"), new MultipleInstanceProperty
		(
			array($instTask_Object_Delete_Title_English),
			array($objLanguageString)
		))
	));
	
	$objObject->CreateProperty("Tasks", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty
	(
		array
		(
			$instTask_Object_Delete
		),
		array($objTask)
	));
?>