<?php
	
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\Objectify;
use Phast\HTMLControls\Layer;
use Phast\WebControls\Button;
use Phast\WebControls\Menu;
use Phast\WebControls\MenuItemCommand;
					
	class ReportListView extends ListView
	{
		/**
		 * The Instance of the Report to display in this ReportListView.
		 * @var Instance
		 */
		public $Report;
		
		protected function RenderContent()
		{
			$div = new Layer();
			$div->ClassList[] = "ListViewControls";
			
			$cmdOptions = new Button();
			$cmdOptions->DropDownDirection = "right";
			$cmdOptions->DropDownRequired = true;
			$cmdOptions->IconName = "cog";
			
			$menuOptions = new Menu();
			$menuOptions->ClassList[] = "Popup";
			$menuOptions->ClassList[] = "Visible";
			$menuOptions->ClassList[] = "Visible-Always";
			
			$cmdOptionsDownload = new MenuItemCommand("Download");
			$cmdOptionsDownload->IconName = "download";
			$menuOptions->Items[] = $cmdOptionsDownload;

			$cmdOptionsPrint = new MenuItemCommand("Print");
			$cmdOptionsPrint->IconName = "print";
			$menuOptions->Items[] = $cmdOptionsPrint;
			
			$cmdOptions->DropDownControls[] = $menuOptions;
			$div->Controls[] = $cmdOptions;
			
			$div->Render();
			
			$rel = $this->Report->GetRelationship(KnownRelationships::get___Report__has__Report_Column());
			if ($rel != null)
			{
				$relDataSource = $this->Report->GetRelationship(KnownRelationships::get___Report__has__Report_Data_Source());
				if ($relDataSource == null) return;
				
				$instDataSource = $relDataSource->GetDestinationInstance();
				if ($instDataSource == null) return;
				
				$relSourceMethod = $instDataSource->GetRelationship(KnownRelationships::get___Report_Data_Source__has_source__Method());
				if ($relSourceMethod == null) return;
				
				$instSourceMethod = $relSourceMethod->GetDestinationInstance();
				$instsRow = Objectify::ExecuteMethod($instSourceMethod);

				$instsReportColumn = $rel->GetDestinationInstances();
				foreach ($instsReportColumn as $instReportColumn)
				{
					$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
					if ($relReportField != null)
					{
						$instReportField = $relReportField->GetDestinationInstance();
						$title = $instReportField->ToString();
						if ($instReportField->ParentObject->Name == "PrimaryObjectReportField")
						{
							$title = $instsRow[0]->ParentObject->Name;
						}
						$this->Columns[] = new ListViewColumn("ch" . $instReportField->ID, $title);
					}
				}
				
				foreach ($instsRow as $instRow)
				{
					$lvi = new ListViewItem();
					$countReportColumn= count($instsReportColumn);
					for ($i = 0; $i < $countReportColumn; $i++)
					{
						$instReportColumn = $instsReportColumn[$i];
							
						$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
						$instReportField = $relReportField->GetDestinationInstance();
							
						$lvi->Columns[] = new ListViewItemColumn($this->Columns[$i]->ID, function($sender)
						{
							$instRow = $sender->ExtraData[0];
							$instReportField = $sender->ExtraData[1];
							$instReportColumn = $sender->ExtraData[2];
			
							$displayAsCount = false;
							$relHas_Report_Column_Option = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Column_Option());
							if ($relHas_Report_Column_Option != null)
							{
								$instsHas_Report_Column_Option = $relHas_Report_Column_Option->GetDestinationInstances();
								foreach ($instsHas_Report_Column_Option as $instRCO)
								{
									if ($instRCO->GlobalIdentifier == "5C9B4C79995B4E6A81C039C174BF9F6D")
									{
										$displayAsCount = true;
									}
								}
							}
			
							$value = Objectify::GetReportFieldValue($instReportField, $instRow);
							if (is_object($value))
							{
								if (get_class($value) == "Objectify\\Objects\\Instance")
								{
									/*
									 if ($value->HasParentObject(KnownObjects::get___Attribute()))
									 {
									 echo($instRow->GetAttributeValue($instAttribute, "(empty)"));
									 }
									 else
									 {
									 */
			
									if ($displayAsCount)
									{
										echo ("1");
									}
									else
									{
										$idw = new InstanceDisplayWidget($value);
										$idw->Render();
									}
								}
							}
							else if (is_array($value))
							{
								if ($displayAsCount)
								{
									echo("<a href=\"#\" class=\"InstanceListDropDown\" data-row-instance-id=\"" . $instRow->GetInstanceID() . "\" data-field-instance-id=\"" . $instReportField->GetInstanceID() . "\">");
									echo (count($value));
									echo (" <i class=\"fa fa-caret-down\"></i></a>");
								}
								else
								{
									foreach ($value as $val)
									{
										if (get_class($val) == "Objectify\\Objects\\Instance")
										{
											$idw = new InstanceDisplayWidget($val);
											$idw->Render();
											echo ("<br />");
										}
										else
										{
											echo ("<!-- GetReportFieldValue not defined for class `" . get_class($val) . "` -->");
										}
									}
								}
							}
							else
							{
								echo ($value);
							}
						}, $instRow->ToString(), array($instRow, $instReportField, $instReportColumn));
					}
					$this->Items[] = $lvi;
				}
			}
			
			parent::RenderContent();
		}
	}
?>