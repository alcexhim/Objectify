<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	
	$tables[] = new Table("PaymentPlans", "paymentplan_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 256, null, true),
		new Column("Description", "LONGTEXT", null, null, true)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Title", "Unpaid"),
			new RecordColumn("Description", "The owner is not paying for the provisioning of this tenant.")
		))
	));
?>