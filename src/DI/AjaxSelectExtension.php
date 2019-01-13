<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2019 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\DI;

class AjaxSelectExtension extends \Nette\DI\CompilerExtension
{
	/**
	 * @param \Nette\PhpGenerator\ClassType $class
	 * @return void
	 */
	public function afterCompile(\Nette\PhpGenerator\ClassType $class) : void
	{
		$initialize = $class->getMethod('initialize');
		$initialize->addBody(__CLASS__ . '::registerControls();');
	}


	/**
	 * @return void
	 */
	public static function registerControls() : void
	{
		\Nette\Forms\Container::extensionMethod('addAjaxSelect', function (\Nette\Forms\Container $container, $name, $label, callable $function) {
			return $container[$name] = new AjaxSelect($label, $function);
		});

		\Nette\Forms\Container::extensionMethod('addMultiAjaxSelect', function (\Nette\Forms\Container $container, $name, $label, callable $function) {
			return $container[$name] = new AjaxMultiSelect($label, $function);
		});
	}
}
