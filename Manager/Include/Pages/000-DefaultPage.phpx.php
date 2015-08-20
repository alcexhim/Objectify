<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\CancelEventArgs;
	
	use Phast\Parser\PhastPage;
	
	class DefaultPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			System::Redirect("~/dashboard");
			$e->Cancel = true;
		}
	}
?>