<?php
namespace Commonhelp\DI;

interface ServiceProviderInterface{
	
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $c An Container instance
	 */
	public function register(ContainerInterface $c);
	
}