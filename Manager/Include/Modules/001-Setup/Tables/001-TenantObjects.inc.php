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
	
	$tblTenantObjects = new Table("TenantObjects", "object_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, false),
		new Column("GlobalIdentifier", "CHAR", 32, null, true)
	));
	$tblTenantObjects->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID"))
	);
	$tables[] = $tblTenantObjects;
	
	// Available static properties for the objects.
	$tblTenantObjectProperties = new Table("TenantObjectProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, true),
		new Column("DataTypeID", "INT", null, null, true),
		new Column("DefaultValue", "LONGTEXT", null, null, true),
		new Column("IsRequired", "INT", null, 0, false),
		new Column("ColumnVisible", "INT", null, 0, false)
	));
	$tblTenantObjectProperties->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("DataTypeID", new TableForeignKeyColumn($tblDataTypes, "ID"))
	);
	$tables[] = $tblTenantObjectProperties;
	
	// Values for static properties of objects.
	$tblTenantObjectPropertyValues = new Table("TenantObjectPropertyValues", "propval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("PropertyID", "INT", null, null, false),
		new Column("Value", "LONGTEXT", null, null, true)
	));
	$tblTenantObjectPropertyValues->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("TenantID"),
		new TableKeyColumn("ObjectID"),
		new TableKeyColumn("PropertyID")
	));
	$tblTenantObjectPropertyValues->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("PropertyID", new TableForeignKeyColumn($tblTenantObjectProperties, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectPropertyValues;
?>