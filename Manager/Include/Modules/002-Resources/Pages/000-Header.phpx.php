<?php
	namespace Objectify\Tenant\Resources\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\System;
	
	class HeaderPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$filename = System::GetApplicationPath() . "/Images/Header_Default.png";
			$mimetype = mime_content_type($filename);
			header("Content-Type: " . $mimetype);
			readfile($filename);
			$e->Cancel = true;
		}
	}
?>