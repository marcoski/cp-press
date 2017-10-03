<?php
namespace Commonhelp\DI\Invoker;

use Commonhelp\DI\Invoker\Exception\InvocationException;
use Commonhelp\DI\Invoker\Exception\NotCallableException;
use Commonhelp\DI\Invoker\Exception\NotEnoughParametersException;

interface InvokerInterface{
	
	/**
	 * Call the given function using the given parameters.
	 *
	 * @param callable $callable   Function to call.
	 * @param array    $parameters Parameters to use.
	 *
	 * @return mixed Result of the function.
	 *
	 * @throws InvocationException Base exception class for all the sub-exceptions below.
	 * @throws NotCallableException
	 * @throws NotEnoughParametersException
	 */
	public function call($callable, array $parameters = array());
	
}