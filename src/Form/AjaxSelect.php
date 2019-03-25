<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types=1);

namespace Nepttune\Form;

class AjaxSelect extends \Nette\Forms\Controls\SelectBox implements \Nette\Application\UI\ISignalReceiver
{
    use TAjaxSelect;

    public const SIGNAL_NAME = 'load';

    public function __construct(?string $label, callable $callback)
    {
        $this->callback = $callback;
        parent::__construct($label);
    }
    
    public function getValue()
    {
        if (!\in_array($this->value, [null, '', []], true)) {
            $this->setValue($this->value);
        }

        return \array_key_exists($this->value, $this->getItems()) ? $this->value : null;
    }
}
