<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	
	use Objectify\Objects\Objectify;
	use Phast\Utilities\Stopwatch;
		
	class TestingPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			header("Content-Type: text/plain");
			
			$sw = new Stopwatch();
			
			$objMethod = TenantObject::GetByName("Method");
			$instMethod = $objMethod->GetInstance(array
			(
				new TenantObjectInstancePropertyValue("Name", "GetLoginTokenForCurrentUser")
			));
			
			echo ("Method name: " . $instMethod->ToString());
			echo ("\r\n");
			
			$sw->start();
			$result = Objectify::ExecuteMethod("GetLoginTokenForCurrentUser");
			$sw->stop();
			
			if ($result == null)
			{
				echo ("Method Return Value is null");
			}
			else
			{
				echo ("Method Return Value: " . $result);
			}
			echo ("\r\n");
			echo ("Method Time: " . $sw->getElapsedTime());
			
			echo ("\r\n");
			echo ("\r\n");
			
			$sw->start();
			$result = $_SESSION["Authentication.LoginToken"];
			$sw->stop();
			
			echo ("Non-Method Expression: \$_SESSION[\"Authentication.LoginToken\"]");
			echo ("\r\n");
			
			echo ("Non-Method Result: " . $result);
			echo ("\r\n");
			echo ("Non-Method Time: " . $sw->getElapsedTime());
			
			die();
		}
	}
?>