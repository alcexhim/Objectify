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
	}
?>