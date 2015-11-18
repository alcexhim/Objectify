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
		new Column("TenantID", "INT", null, null, true), // if set, object is only visible/referencable within specified tenant
		new Column("Name", "VARCHAR", 256, null, false)
	));
	$tblTenantObjects->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, $tblTenants->GetColumnByName("ID")))
	);
	$tables[] = $tblTenantObjects;
	
	$tblTenantObjectParentObjects = new Table("TenantObjectParentObjects", "parentobject_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ObjectID", "INT", null, null, false),
		new Column("ParentObjectID", "INT", null, null, false)
	));
	$tblTenantObjectParentObjects->ForeignKeys = array
	(
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("ParentObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectParentObjects;
	
	// Available static properties for the objects.
	$tblTenantObjectProperties = new Table("TenantObjectProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, true),
		new Column("DataTypeID", "INT", null, null, true),
		new Column("DefaultValue", "LONGBLOB", null, null, true),
		new Column("IsRequired", "INT", null, 0, false),
		new Column("ColumnVisible", "INT", null, 0, false)
	));
	$tblTenantObjectProperties->ForeignKeys = array
	(
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("DataTypeID", new TableForeignKeyColumn($tblDataTypes, "ID"))
	);
	$tables[] = $tblTenantObjectProperties;
	
	// Values for static properties of objects.
	$tblTenantObjectPropertyValues = new Table("TenantObjectPropertyValues", "propval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("PropertyID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Value", "LONGBLOB", null, null, true)
	));
	$tblTenantObjectPropertyValues->ForeignKeys = array
	(
		new TableForeignKey("PropertyID", new TableForeignKeyColumn($tblTenantObjectProperties, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectPropertyValues;
	
	// Instances of the objects.
	$tblTenantObjectInstances = new Table("TenantObjectInstances", "instance_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, false)
	));
	$tblTenantObjectInstances->ForeignKeys = array
	(
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectInstances;
	
	// Properties of the object instances.
	$tblTenantObjectInstanceProperties = new Table("TenantObjectInstanceProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, true),
		new Column("DataTypeID", "INT", null, null, true),
		new Column("DefaultValue", "LONGBLOB", null, null, true),
		new Column("IsRequired", "INT", null, 0, false),
		new Column("ColumnVisible", "INT", null, 0, false)
	));
	$tblTenantObjectInstanceProperties->ForeignKeys = array
	(
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("DataTypeID", new TableForeignKeyColumn($tblDataTypes, "ID"))
	);
	$tables[] = $tblTenantObjectInstanceProperties;
	
	// Values of the object instance properties.
	$tblTenantObjectInstancePropertyValues = new Table("TenantObjectInstancePropertyValues", "propval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("InstanceID", "INT", null, null, false),
		new Column("PropertyID", "INT", null, null, false),
		new Column("Value", "LONGBLOB", null, null, false)
	));
	$tblTenantObjectInstancePropertyValues->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("InstanceID"),
		new TableKeyColumn("PropertyID")
	));
	$tblTenantObjectInstancePropertyValues->ForeignKeys = array
	(
		new TableForeignKey("InstanceID", new TableForeignKeyColumn($tblTenantObjectInstances, "ID")),
		new TableForeignKey("PropertyID", new TableForeignKeyColumn($tblTenantObjectInstanceProperties, "ID"))
	);
	$tables[] = $tblTenantObjectInstancePropertyValues;
	
	// Object static methods.
	$tblTenantObjectMethods = new Table("TenantObjectMethods", "method_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, false),
		new Column("CodeBlob", "LONGBLOB", null, null, false)
	));
	$tblTenantObjectMethods->ForeignKeys = array
	(
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectMethods;
	
	// Object static method namespace references.
	$tblTenantObjectMethodNamespaceReferences = new Table("TenantObjectMethodNamespaceReferences", "ns_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("MethodID", "INT", null, null, false),
		new Column("Value", "VARCHAR", 256, null, false)
	));
	$tblTenantObjectMethodNamespaceReferences->ForeignKeys = array
	(
		new TableForeignKey("MethodID", new TableForeignKeyColumn($tblTenantObjectMethods, "ID"))
	);
	$tables[] = $tblTenantObjectMethodNamespaceReferences;
	
	// Object instance methods.
	$tblTenantObjectInstanceMethods = new Table("TenantObjectInstanceMethods", "method_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, false),
		new Column("CodeBlob", "LONGBLOB", null, null, false)
	));
	$tables[] = $tblTenantObjectInstanceMethods;
	
	// Object static method namespace references.
	$tblTenantObjectInstanceMethodNamespaceReferences = new Table("TenantObjectInstanceMethodNamespaceReferences", "ns_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("MethodID", "INT", null, null, false),
		new Column("Value", "VARCHAR", 256, null, false)
	));
	$tables[] = $tblTenantObjectInstanceMethodNamespaceReferences;
?>