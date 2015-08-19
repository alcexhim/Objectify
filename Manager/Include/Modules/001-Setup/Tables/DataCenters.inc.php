<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	use Phast\Data\TableForeignKeyReferenceOption;
	
	$tables[] = new Table("DataCenters", "datacenter_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("Description", "LONGTEXT", null, null, true),
		new Column("HostName", "VARCHAR", 256, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Default"),
			new RecordColumn("Description", "The initial data center configured for PhoenixSNS tenanted hosting."),
			new RecordColumn("HostName", "www.yourdomain.com")
		))
	));
?>