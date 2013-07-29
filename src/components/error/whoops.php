<?php	

	namespace Whoops\Fluorine;
	use Whoops\Run;
	use Whoops\Handler\PrettyPageHandler;
	use Exception as BaseException;


	$run     = new Run;
	$handler = new PrettyPageHandler;

	$run->register()->pushHandler($handler);