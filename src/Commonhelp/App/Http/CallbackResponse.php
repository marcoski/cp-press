<?php
namespace Commonhelp\App\Http;

interface CallbackResponse {
	
	function callback(OutputInterface $output);
}