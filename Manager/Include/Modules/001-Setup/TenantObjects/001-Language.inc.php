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

	$objLanguageString = TenantObject::Create("LanguageString", $objObject);
	$objLanguage = TenantObject::Create("Language", $objObject);

	$objObject->CreateProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	
	$objLanguage->CreateInstanceProperty("LocaleID", DataType::GetByName("Text"));
	$objLanguage->CreateInstanceProperty("Code", DataType::GetByName("Text"));
	$objLanguage->CreateInstanceProperty("Title", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objLanguageString)));
	
	$objLanguageString->CreateInstanceProperty("Language", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objLanguage)));
	$objLanguageString->CreateInstanceProperty("Value", DataType::GetByName("Text"));
	
	$instLanguageEnglish = $objLanguage->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguage->GetInstanceProperty("LocaleID"), "1033"),
		new TenantObjectInstancePropertyValue($objLanguage->GetInstanceProperty("Code"), "en-US")
	));
	
	$instLanguageEnglish_Title = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "English (United States)")
	));
	
	$instLanguageEnglish->SetPropertyValue($objLanguage->GetInstanceProperty("Title"), new MultipleInstanceProperty
	(
		array($instLanguageEnglish_Title),
		array($objLanguageString)
	));
?>
