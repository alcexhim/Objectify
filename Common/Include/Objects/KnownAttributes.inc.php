<?php
	namespace Objectify\Objects;
	
	class KnownAttributes
	{
		const GUID___Attribute___Text___Value = "{041DD7FD-2D9C-412B-8B9D-D7125C166FE0}";
		
		private static $Attribute___Text___Value;
		public static function get___Text___Value()
		{
			if (KnownAttributes::$Attribute___Text___Value == null)
			{
				KnownAttributes::$Attribute___Text___Value = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___Value);
			}
			return KnownAttributes::$Attribute___Text___Value;
		}

		const GUID___Attribute___Text___CSSValue = "{C0DD4A42-F503-4EB3-8034-7C428B1B8803}";
		
		private static $Attribute___Text___CSSValue;
		public static function get___Text___CSSValue()
		{
			if (KnownAttributes::$Attribute___Text___CSSValue == null)
			{
				KnownAttributes::$Attribute___Text___CSSValue = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___CSSValue);
			}
			return KnownAttributes::$Attribute___Text___CSSValue;
		}
		
		const GUID___Attribute___Boolean___Render_as_Text = "{15DD9E49-AB6D-44F0-9039-27AF81736406}";
		
		private static $Attribute___Boolean___Render_as_Text;
		public static function get___Boolean___Render_as_Text()
		{
			if (KnownAttributes::$Attribute___Boolean___Render_as_Text == null)
			{
				KnownAttributes::$Attribute___Boolean___Render_as_Text = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Boolean___Render_as_Text);
			}
			return KnownAttributes::$Attribute___Boolean___Render_as_Text;
		}
		
		const GUID___Attribute___Text___Target_URL = "{970F79A0-9EFE-4E7D-9286-9908C6F06A67}";
		
		private static $Attribute___Text___Target_URL;
		public static function get___Text___Target_URL()
		{
			if (KnownAttributes::$Attribute___Text___Target_URL == null)
			{
				KnownAttributes::$Attribute___Text___Target_URL = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___Target_URL);
			}
			return KnownAttributes::$Attribute___Text___Target_URL;
		}
		
		const GUID___Attribute___Text___BackgroundColor = "{B817BE3B-D0AC-4A60-A98A-97F99E96CC89}";
		private static $Attribute___Text___BackgroundColor;
		public static function get___Text___BackgroundColor()
		{
			if (KnownAttributes::$Attribute___Text___BackgroundColor == null)
			{
				KnownAttributes::$Attribute___Text___BackgroundColor = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___BackgroundColor);
			}
			return KnownAttributes::$Attribute___Text___BackgroundColor;
		}
		
		const GUID___Attribute___Text___ForegroundColor = "{BB4B6E0D-D9BA-403D-9E81-93E8F7FB31C8}";
		private static $Attribute___Text___ForegroundColor;
		public static function get___Text___ForegroundColor()
		{
			if (KnownAttributes::$Attribute___Text___ForegroundColor == null)
			{
				KnownAttributes::$Attribute___Text___ForegroundColor = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___ForegroundColor);
			}
			return KnownAttributes::$Attribute___Text___ForegroundColor;
		}
		
		const GUID___Attribute___Date___CreationDate = "{0DD670AF-498D-4FEC-A2CF-20E3E56EB732}";
		private static $Attribute___Date___CreationDate;
		public static function get___Date___CreationDate()
		{
			if (KnownAttributes::$Attribute___Date___CreationDate == null)
			{
				KnownAttributes::$Attribute___Date___CreationDate = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Date___CreationDate);
			}
			return KnownAttributes::$Attribute___Date___CreationDate;
		}
		
	}
?>