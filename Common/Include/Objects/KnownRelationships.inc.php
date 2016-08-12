<?php
	namespace Objectify\Objects;
	
	class KnownRelationships
	{
		const Relationship___Class__has__Attribute = "{DECBB61A-2C6C-4BC8-9042-0B5B701E08DE}";
		const Relationship___Attribute__for__Class = "{FFC8E435-B9F8-4495-8C85-4DDA67F7E2A8}";
		public static function get___Class__has__Attribute()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Attribute);
		}
		public static function get___Attribute__for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Attribute__for__Class);
		}

		const Relationship___Relationship__has_sibling__Relationship = "{656110FF-4502-48B8-A7F3-D07F017AEA3F}";
		const Relationship___Relationship__sibling_for__Relationship = "{FA08B2A4-71E2-44CB-9252-8CE336E2E1AD}";
		public static function get___Relationship__has_sibling__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_sibling__Relationship);
		}
		public static function get___Relationship__sibling_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__sibling_for__Relationship);
		}
		
		const Relationship___Class__instance_labeled_by__String = "{F52FC851-D655-48A9-B526-C5FE0D7A29D2}";
		const Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value = "{F9B60C00-FF1D-438F-AC74-6EDFA8DD7324}";
		const Relationship___Class__has__Object_Source = "{B62F9B81-799B-4ABE-A4AF-29B45347DE54}";
		const Relationship___Object_Source__for__Class = "{FBB9391D-C4A2-4326-9F85-7801F377253C}";
		
		const Relationship___Tenant__has__Tenant_Type = "{E94B6C9D-3307-4858-9726-F24B7DB21E2D}";
		const Relationship___Tenant_Type__for__Tenant = "{AA858424-859B-42B3-A76D-FDA986C83845}";
		public static function get___Tenant__has__Tenant_Type()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Tenant__has__Tenant_Type);
		}
		public static function get___Tenant_Type__for__Tenant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Tenant_Type__for__Tenant);
		}
		
		const Relationship___Tenant__has_sidebar__Menu_Item = "{D62DFB9F-48D5-4697-AAAD-1CAD0EA7ECFA}";
		const Relationship___Menu_Item__sidebar_for__Tenant = "{4E0A8C3D-5D10-44E5-A6D7-31F262711E01}";
		

		const Relationship___Class__has_title__Translatable_Text_Constant = "{B8BDB905-69DD-49CD-B557-0781F7EF2C50}";
		public static function get___Class__has_title__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Class__has_title__Translatable_Text_Constant);
			return $inst;
		}
		
		public static function get___Class__instance_labeled_by__String()
		{
			$instRelationship__Class__instance_labeled_by_String = Instance::GetByGlobalIdentifier(self::Relationship___Class__instance_labeled_by__String);
			return $instRelationship__Class__instance_labeled_by_String;
		}
		public static function get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value()
		{
			$instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value);
			return $instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value;
		}
		public static function get___Class__has__Object_Source()
		{
			$instRelationship__Class__has__Object_Source = Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Object_Source);
			return $instRelationship__Class__has__Object_Source;
		}
		public static function get___Object_Source__for__Class()
		{
			$instRelationship__Object_Source__for__Class = Instance::GetByGlobalIdentifier(self::Relationship___Object_Source__for__Class);
			return $instRelationship__Object_Source__for__Class;
		}
		public static function get___Tenant__has_sidebar__Menu_Item()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Tenant__has_sidebar__Menu_Item);
			return $inst;
		}
		public static function get___Menu_Item__sidebar_for__Tenant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item__sidebar_for__Tenant);
			return $inst;
		}

		const Relationship___User__has_display_name__Translatable_Text_Constant = "{6C29856C-3B10-4F5B-A291-DD3CA4C04A2F}";
		const Relationship___Translatable_Text_Constant__display_name_for__User = "{C0B4140B-92C1-4D52-B6DA-8E818A83F3FA}";
		public static function get___User__has_display_name__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has_display_name__Translatable_Text_Constant);
			return $inst;
		}
		public static function get___Translatable_Text_Constant__display_name_for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__display_name_for__User);
			return $inst;
		}

		const Relationship___User__has__Security_Group = "{1921D642-84EC-4E16-B568-9C333909C018}";
		const Relationship___Security_Group__for__User = "{EDC79A23-3324-472D-B2F8-8CB9D2CF9C5D}";
		public static function get___User__has__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has__Security_Group);
			return $inst;
		}
		public static function get___Security_Group__for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__for__User);
			return $inst;
		}

		const Relationship___Security_Group__has__Security_Permission = "{5E305133-1380-4736-93E4-6B280B35FCE3}";
		const Relationship___Security_Permission__for__Security_Group = "{52DAC6FC-7E3A-4B09-B3FC-205FF38FE952}";
		public static function get___Security_Group__has__Security_Permission()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__has__Security_Permission);
			return $inst;
		}
		public static function get___Security_Permission__for__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Permission__for__Security_Group);
			return $inst;
		}
		
		const Relationship___Class__has_owner__User = "{D1A25625-C90F-4A73-A6F2-AFB530687705}";
		const Relationship___User__owner_for__Class = "{04DD2E6B-EA57-4840-8DD5-F0AA943C37BB}";
		public static function get___Class__has_owner__User()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has_owner__User);
		}
		public static function get___User__owner_for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User__owner_for__Class);
		}
		
		const Relationship___Extract_Single_Instance_String_Component__has__Relationship = "{5E499753-F50F-4A9E-BF53-DC013820499C}";
		const Relationship___Relationship__for__Extract_Single_Instance_String_Component = "{B0111132-8721-405C-967B-0BEFA92CFE9A}";
		public static function get___Extract_Single_Instance_String_Component__has__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Extract_Single_Instance_String_Component__has__Relationship);
		}
		public static function get___Relationship__for__Extract_Single_Instance_String_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__for__Extract_Single_Instance_String_Component);
		}
		
		const Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant = "{65E3C87E-A2F7-4A33-9FA7-781EFA801E02}";
		const Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command = "{901A5427-6344-40F4-B81C-D905EA152EB9}";
		public static function get___Menu_Item_Command__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Menu_Item_Command()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command);
		}
		
		const Relationship___Relationship__has_source__Class = "{7FB5D234-042E-45CB-B11D-AD72F8D45BD3}";
		const Relationship___Class__source_for__Relationship = "{20FFFDE8-11A5-48D6-894B-21C6B234B811}";
		public static function get___Relationship__has_source__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_source__Class);
		}
		public static function get___Class__source_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__source_for__Relationship);
		}
		const Relationship___Relationship__has_destination__Class = "{F220F1C2-0499-4E87-A32E-BDBF80C1F8A4}";
		const Relationship___Class__destination_for__Relationship = "{A66CD08C-A155-42AF-8995-A1D96C5A0C06}";
		public static function get___Relationship__has_destination__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_destination__Class);
		}
		public static function get___Class__destination_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__destination_for__Relationship);
		}
		
		const Relationship___String__has__String_Component = "{3B6C4C25-B7BC-4242-8ED1-BA6D01B834BA}";
		const Relationship___String_Component__for__String = "{40E17597-0AEB-4C56-BBAB-FC600E1196DD}";
		public static function get___String__has__String_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___String__has__String_Component);
		}
		public static function get___String_Component__for__String()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___String_Component__for__String);
		}
		
		const Relationship___Translatable_Text_Constant_Value__has__Language = "{3655AEC2-E2C9-4DDE-8D98-0C4D3CE1E569}";
		const Relationship___Language__for__Translatable_Text_Constant_Value = "{032C3549-C2FC-4512-B98A-C2D0BBCF78D0}";
		public static function get___Translatable_Text_Constant_Value__has__Language()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant_Value__has__Language);
		}
		public static function get___Language__for__Translatable_Text_Constant_Value()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Language__for__Translatable_Text_Constant_Value);
		}
		
		const Relationship___Class__has__Task = "{4D8670E1-2AF1-4E7C-9C87-C910BD7B319B}";
		const Relationship___Task__for__Class = "{F6D05235-AAA8-4DC0-8D3A-A0F336B39F01}";
		public static function get___Class__has__Task()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Task);
		}
		public static function get___Task__for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Task__for__Class);
		}
		


		const Relationship___Method__has__Method_Binding = "{D52500F1-1421-4B73-9987-223163BC9C04}";
		const Relationship___Method_Binding__for__Method = "{B782A592-8AF5-4228-8296-E3D0B24C70A8}";
		public static function get___Method__has__Method_Binding()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Method__has__Method_Binding);
		}
		public static function get___Method_Binding__for__Method()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Method_Binding__for__Method);
		}

		const Relationship___Return_Instance_Set_Method_Binding__has_source__Class = "{EE7A3049-8E09-410C-84CB-C2C0D652CF40}";
		const Relationship___Class__source_for__Return_Instance_Set_Method_Binding = "{AA3C3ECA-9963-4877-9D9E-139724B59E14}";
		public static function get___Return_Instance_Set_Method_Binding__has_source__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Return_Instance_Set_Method_Binding__has_source__Class);
		}
		public static function get___Class__source_for__Return_Instance_Set_Method_Binding()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__source_for__Return_Instance_Set_Method_Binding);
		}
		
		
		const Relationship___User_Login__has__User = "{85B40E4B-849B-4006-A9C0-4E201B25975F}";
		const Relationship___User__for__User_Login = "{C79A6041-FC94-41A5-9860-D443C60FA7DE}";
		public static function get___User_Login__has__User()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User_Login__has__User);
		}
		public static function get___User__for__User_Login()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User__for__User_Login);
		}
		
		const Relationship___Sequential_Container_Page_Component__has__Sequential_Container_Orientation = "{DD55F506-8718-4240-A894-21346656E804}";
		const Relationship___Sequential_Container_Orientation__for__Sequential_Container_Page_Component = "{F8F4EBFE-605C-4F68-99F7-83AEF3FF1AF2}";
		public static function get___Sequential_Container_Page_Component__has__Sequential_Container_Orientation()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Page_Component__has__Sequential_Container_Orientation);
		}
		public static function get___Sequential_Container_Orientation__for__Sequential_Container_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Orientation__for__Sequential_Container_Page_Component);
		}
		
		
		
		

		const Relationship___Panel_Page_Component__has_header__Page_Component = "{223B4073-F417-49CD-BCA1-0E0749144B9D}";
		const Relationship___Page_Component__header_for__Panel_Page_Component = "{38E70546-EB2F-4A2E-A2FA-9E290C1835A8}";
		public static function get___Panel_Page_Component__has_header__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_header__Page_Component);
		}
		public static function get___Page_Component__header_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__header_for__Panel_Page_Component);
		}

		const Relationship___Panel_Page_Component__has_content__Page_Component = "{AD8C5FAE-2444-4700-896E-C5F968C0F85B}";
		const Relationship___Page_Component__content_for__Panel_Page_Component = "{1DD64A89-9947-4BA3-85AA-12D2EAED80DC}";
		public static function get___Panel_Page_Component__has_content__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_content__Page_Component);
		}
		public static function get___Page_Component__content_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__content_for__Panel_Page_Component);
		}
		
		const Relationship___Panel_Page_Component__has_footer__Page_Component = "{56E339BD-6189-4BAC-AB83-999543FB8060}";
		const Relationship___Page_Component__footer_for__Panel_Page_Component = "{9DC77CE3-869D-4E6C-A649-7A662FB5026D}";
		public static function get___Panel_Page_Component__has_footer__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_footer__Page_Component);
		}
		public static function get___Page_Component__footer_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__footer_for__Panel_Page_Component);
		}
		
		


		const Relationship___Sequential_Container_Page_Component__has__Page_Component = "{CB7B8162-1C9E-4E72-BBB8-C1C37CA69CD5}";
		const Relationship___Page_Component__for__Sequential_Container_Page_Component = "{E33F11EE-4417-4890-AD89-7BB3DB739918}";
		public static function get___Sequential_Container_Page_Component__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Page_Component__has__Page_Component);
		}
		public static function get___Page_Component__for__Sequential_Container_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__for__Sequential_Container_Page_Component);
		}
		
		const Relationship___Page__has__Style = "{6E6E1A85-3EA9-4939-B13E-CBF645CB8B59}";
		const Relationship___Style__for__Page = "{A608FC55-4D41-47F6-B021-38DFBAF29521}";
		public static function get___Page__has__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page__has__Style);
		}
		public static function get___Style__for__Page()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__for__Page);
		}
		
		const Relationship___Page__has__Page_Component = "{24F6C596-D77D-4754-B023-00321DEBA924}";
		const Relationship___Page_Component__for__Page = "{2519A689-1184-4E24-8006-22FE3F7DB229}";
		public static function get___Page__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page__has__Page_Component);
		}
		public static function get___Page_Component__for__Page()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__for__Page);
		}

		const Relationship___Page_Component__has__Style = "{818CFF50-7D42-43B2-B6A7-92C3C54D450D}";
		const Relationship___Style__has__Page_Component = "{007563E7-7277-4436-8C82-06D5F156D8E1}";
		public static function get___Page_Component__has__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__has__Style);
		}
		public static function get___Style__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__has__Page_Component);
		}
		
		const Relationship___Heading_Page_Component__has_text__Translatable_Text_Constant = "{C5027DC2-53EE-4FC0-9BA6-F2B883F7DAD8}";
		const Relationship___Translatable_Text_Constant__text_for__Heading_Page_Component = "{29C02384-57B0-45F5-9C15-747F9DFD2C69}";
		public static function get___Heading_Page_Component__has_text__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Heading_Page_Component__has_text__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__text_for__Heading_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__text_for__Heading_Page_Component);
		}
		
		const Relationship___Paragraph_Page_Component__has_text__Translatable_Text_Constant = "{0E002E6F-AA79-457C-93B8-2CCE1AEF5F7E}";
		const Relationship___Translatable_Text_Constant__text_for__Paragraph_Page_Component = "{5E75000D-2421-4AD4-9E5F-B9FDD9CB4744}";
		public static function get___Paragraph_Page_Component__has_text__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Paragraph_Page_Component__has_text__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__text_for__Paragraph_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__text_for__Paragraph_Page_Component);
		}
		
		const Relationship___Command_Menu_Item__has__Icon = "{8859DAEF-01F7-46FA-8F3E-7B2F28E0A520}";
		const Relationship___Icon__for__Command_Menu_Item = "{6D3D6F9E-CDAE-4D0E-B5B2-BC2BA333F746}";
		public static function get___Command_Menu_Item__has__Icon()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Command_Menu_Item__has__Icon);
		}
		public static function get___Icon__for__Command_Menu_Item()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Icon__for__Command_Menu_Item);
		}
		
		const Relationship___Style__has__Style_Rule = "{4CC8A654-B2DF-4B17-A956-24939530790E}";
		const Relationship___Style_Rule__for__Style = "{32276525-126A-412F-A10F-1368312D2EAB}";
		public static function get___Style__has__Style_Rule()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__has__Style_Rule);
		}
		public static function get___Style_Rule__for__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Rule__for__Style);
		}
		
		const Relationship___Style_Rule__has__Style_Property = "{B69C2708-E78D-413A-B491-ABB6F1D2A6E0}";
		const Relationship___Style_Property__for__Style_Rule = "{DF43F7A5-8175-4E23-9801-8299EA55B356}";
		public static function get___Style_Rule__has__Style_Property()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Rule__has__Style_Property);
		}
		public static function get___Style_Property__for__Style_Rule()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Property__for__Style_Rule);
		}
		
		const Relationship___Class__has_summary__Report_Field = "{D11050AD-7376-4AB7-84DE-E8D0336B74D2}";
		const Relationship___Report_Field__summary_for__Class = "{FAD8F8B8-B0E2-4852-A6E9-A272C041DC4E}";
		public static function get___Class__has_summary__Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has_summary__Report_Field);
		}
		public static function get___Report_Field__summary_for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Field__summary_for__Class);
		}
		
		const Relationship___Report__has_title__Translatable_Text_Constant = "{DF93EFB0-8B5E-49E7-8BC0-553F9E7602F9}";
		const Relationship___Translatable_Text_Constant__title_for__Report = "{2C4EA43B-6242-4370-9FF2-A78994702BCD}";
		public static function get___Report__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Report()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Report);
		}
		
		const Relationship___Report__has__Report_Field = "{7A8F57F1-A4F3-4BAF-84A5-E893FD79447D}";
		const Relationship___Report_Field__for__Report = "{3FA19854-CF0F-4656-B80B-54EF633503E0}";
		public static function get___Report__has__Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report__has__Report_Field);
		}
		public static function get___Report_Field__for__Report()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Field__for__Report);
		}
		
		const Relationship___Report__has__Report_Data_Source = "{1DE7B484-F9E3-476A-A9D3-7D2A86B55845}";
		const Relationship___Report_Data_Source__for__Report = "{05DA3D00-0EAE-454E-A7D9-C7382EDCD15F}";
		public static function get___Report__has__Report_Data_Source()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report__has__Report_Data_Source);
		}
		public static function get___Report_Data_Source__for__Report()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Data_Source__for__Report);
		}
		
		const Relationship___Report_Data_Source__has_source__Method = "{2D5CB496-5839-46A0-9B94-30D4E2227B56}";
		const Relationship___Method__source_for__Report_Data_Source = "{1A043080-7AD0-4CCF-AED7-CD88CAA15D94}";
		public static function get___Report_Data_Source__has_source__Method()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Data_Source__has_source__Method);
		}
		public static function get___Method__source_for__Report_Data_Source()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Method__source_for__Report_Data_Source);
		}
		
		const Relationship___Report__has__Report_Facet = "{EA7C6841-FFE0-4230-95CF-87B3D425DC38}";
		const Relationship___Report_Facet__for__Report = "{EC515BC0-D235-4A1F-A461-72B840B49020}";
		public static function get___Report__has__Report_Facet()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report__has__Report_Facet);
		}
		public static function get___Report_Facet__for__Report()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Facet__for__Report);
		}
		
		const Relationship___Report_Facet__has__Report_Facet_Option = "{9EE30D06-5BF3-406F-B13A-451F5A609046}";
		const Relationship___Report_Facet_Option__for__Report_Facet = "{F21B8F49-9F19-497C-9A32-10601BC6FB0F}";
		public static function get___Report_Facet__has__Report_Facet_Option()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Facet__has__Report_Facet_Option);
		}
		public static function get___Report_Facet_Option__for__Report_Facet()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Facet_Option__for__Report_Facet);
		}
		
		const Relationship___Report_Field__has_title__Translatable_Text_Constant = "{6780BFC2-DBC0-40AE-83EE-BFEF979F0054}";
		const Relationship___Translatable_Text_Constant__title_for__Report_Field = "{8AAAEF67-2CB8-4CC1-B854-F95E353848D1}";
		public static function get___Report_Field__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Field__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Report_Field);
		}
		
		const Relationship___Attribute_Report_Field__has_target__Attribute = "{37964301-26FD-41D8-8661-1F73684C0E0A}";
		const Relationship___Attribute__target_for__Attribute_Report_Field = "{09E6A707-9833-4167-B563-4F232BE2E29D}";
		public static function get___Attribute_Report_Field__has_target__Attribute()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Attribute_Report_Field__has_target__Attribute);
		}
		public static function get___Attribute__target_for__Attribute_Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Attribute__target_for__Attribute_Report_Field);
		}
		
		const Relationship___Relationship_Report_Field__has_target__Relationship = "{134B2790-F6DF-4F97-9AB5-9878C4A715E5}";
		const Relationship___Relationship__target_for__Relationship_Report_Field = "{AF8AB7F2-5581-4891-8C6F-92ACD8AFB1B4}";
		public static function get___Relationship_Report_Field__has_target__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship_Report_Field__has_target__Relationship);
		}
		public static function get___Relationship__target_for__Relationship_Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__target_for__Relationship_Report_Field);
		}
		
		const Relationship___Task__has_title__Translatable_Text_Constant = "{D97AE03C-261F-4060-A06D-985E26FA662C}";
		const Relationship___Translatable_Text_Constant__title_for__Task = "{4E5D363F-6984-4BD5-8E76-AAB598C1B09D}";
		public static function get___Task__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Task__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Task()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Task);
		}
		
		const Relationship___Prompt__has_title__Translatable_Text_Constant = "{6B25BD5F-9B06-42D1-ACC6-2FA1A2248965}";
		const Relationship___Translatable_Text_Constant__title_for__Prompt = "{9FE0E4B7-875A-4F43-9037-E1D443D4F798}";
		public static function get___Prompt__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Prompt__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Prompt()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Prompt);
		}
		
		const Relationship___Task__has__Prompt = "{929B106F-7E3E-4D30-BB84-E450A4FED063}";
		const Relationship___Prompt__for__Task = "{146616A0-A64A-402B-99FB-7548D92E5CBC}";
		public static function get___Task__has__Prompt()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Task__has__Prompt);
		}
		public static function get___Prompt__for__Task()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Prompt__for__Task);
		}
		
		const Relationship___Choice_Prompt__has_valid__Prompt_Value = "{6A260FA9-5F60-442B-8860-814F8EFEA059}";
		const Relationship___Prompt_Value__valid_for__Choice_Prompt = "{C147D288-4911-4779-9CD0-1E0CA20384D4}";
		public static function get___Choice_Prompt__has_valid__Prompt_Value()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Choice_Prompt__has_valid__Prompt_Value);
		}
		public static function get___Prompt_Value__valid_for__Choice_Prompt()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Prompt_Value__valid_for__Choice_Prompt);
		}
		
		const Relationship___Prompt_Value__has_title__Translatable_Text_Constant = "{44659235-BCC3-4674-B813-52D58BFA85D7}";
		const Relationship___Translatable_Text_Constant__title_for__Prompt_Value = "{28C32F20-C317-4C48-B314-378440D68C0F}";
		public static function get___Prompt_Value__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Prompt_Value__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Prompt_Value()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Prompt_Value);
		}
		
		const Relationship___Instance_Prompt__has_valid__Class = "{D5BD754B-F401-4FD8-A707-82684E7E25F0}";
		const Relationship___Class__valid_for__Instance_Prompt = "{9D7628CC-D888-4311-A5F7-6EEA17FF6A86}";
		public static function get___Instance_Prompt__has_valid__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Instance_Prompt__has_valid__Class);
		}
		public static function get___Class__valid_for__Instance_Prompt()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__valid_for__Instance_Prompt);
		}
		
		const Relationship___Relationship_Editor_Page_Component__has__Report_Field = "{1540634D-EB44-48F8-88B8-17B7DCBBD006}";
		const Relationship___Report_Field__for__Relationship_Editor_Page_Component = "{51C8837D-FF85-4647-A0F6-BE5D0AED51B5}";
		public static function get___Relationship_Editor_Page_Component__has__Report_Field()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship_Editor_Page_Component__has__Report_Field);
		}
		public static function get___Report_Field__for__Relationship_Editor_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Report_Field__for__Relationship_Editor_Page_Component);
		}
		
		const Relationship___Tenant__has_logo_image__File = "{4C399E80-ECA2-4A68-BFB4-26A5E6E97047}";
		const Relationship___File__logo_image_for__Tenant = "{A6E61265-6681-4AA7-A183-BEDF27DC5CD8}";
		public static function get___Tenant__has_logo_image__File()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Tenant__has_logo_image__File);
		}
		public static function get___File__logo_image_for__Tenant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___File__logo_image_for__Tenant);
		}
		
	}
?>