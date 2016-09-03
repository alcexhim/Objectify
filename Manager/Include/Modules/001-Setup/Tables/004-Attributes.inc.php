<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	use Phast\Data\TableKey;
	use Phast\Data\TableKeyColumn;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	use Phast\Data\TableForeignKeyReferenceOption;
	
	// Instances of the objects.
	$tblAttributeValues = new Table("AttributeValues", "attval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("AttributeTenantID", "INT", null, null, true),
		new Column("AttributeObjectID", "INT", null, null, false),
		new Column("AttributeInstanceID", "INT", null, null, false),
		new Column("InstanceID", "INT", null, null, false),
		new Column("EffectiveDateTime", "DATETIME", null, null, false),
		new Column("UserTenantID", "INT", null, null, true),
		new Column("UserObjectID", "INT", null, null, true),
		new Column("UserInstanceID", "INT", null, null, true),
		new Column("Value", "LONGTEXT", null, null, false)
	));
	$tblAttributeValues->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("TenantID"),
		// new TableKeyColumn("AttributeTenantID"),
		new TableKeyColumn("AttributeObjectID"),
		new TableKeyColumn("AttributeInstanceID"),
		new TableKeyColumn("InstanceID"),
		new TableKeyColumn("EffectiveDateTime")
	));
	$tblAttributeValues->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("AttributeTenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("AttributeObjectID", new TableForeignKeyColumn($tblInstances, "ID")),
		new TableForeignKey("AttributeInstanceID", new TableForeignKeyColumn($tblInstances, "ID")),
		new TableForeignKey("UserTenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("UserObjectID", new TableForeignKeyColumn($tblInstances, "ID")),
		new TableForeignKey("UserInstanceID", new TableForeignKeyColumn($tblInstances, "ID")),
		new TableForeignKey("InstanceID", new TableForeignKeyColumn($tblInstances, "ID"))
	);
	$tables[] = $tblAttributeValues;
?>