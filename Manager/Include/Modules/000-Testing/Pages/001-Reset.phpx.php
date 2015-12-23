<?php
	namespace Objectify\Manager\Modules\TestingModule\Pages;
	
	use Phast\CancelEventArgs;
	
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use PDO;
	
	use Phast\System;
	
	class ResetPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if ($_POST["Confirmation"] == "1")
				{
					$pdo = DataSystem::GetPDO();
					
					$statement = $pdo->prepare("SET foreign_key_checks = 0");
					$result = $statement->execute();
					
					$statement = $pdo->prepare("SHOW TABLES");
					$result = $statement->execute();
					$count = $statement->rowCount();
					
					$query = "DROP TABLE ";
					for ($i = 0; $i < $count; $i++)
					{
						$values = $statement->fetch(PDO::FETCH_NUM);
						$query .= $values[0];
						if ($i < $count - 1) $query .= ", ";
					}
					
					$statement = $pdo->prepare($query);
					$result = $statement->execute();
					
					$statement = $pdo->prepare("SET foreign_key_checks = 1");
					$result = $statement->execute();
					
					System::Redirect("~/");
				}
			}
		}
	}
?>