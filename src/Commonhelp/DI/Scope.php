<?php
namespace Commonhelp\DI;

class Scope{
	
	/**
	 * A singleton entry will be computed once and shared.
	 *
	 * For a class, only a single instance of the class will be created.
	 */
	const SINGLETON = 'singleton';
	/**
	 * A prototype entry will be recomputed each time it is asked.
	 *
	 * For a class, this will create a new instance each time.
	 */
	const PROTOTYPE = 'prototype';
	
}