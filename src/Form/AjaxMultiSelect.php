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

declare(strict_types=1);

namespace Nepttune\Form;

class AjaxMultiSelect extends \Nette\Forms\Controls\MultiSelectBox implements \Nette\Application\UI\ISignalReceiver
{
    use TAjaxSelect;

    public const CALLBACK_SIGNAL_NAME = AjaxSelect::CALLBACK_SIGNAL_NAME;
    public const ONCHANGE_SIGNAL_NAME = AjaxSelect::ONCHANGE_SIGNAL_NAME;

    public function __construct(?string $label, callable $callback, callable $onchange = null)
    {
        $this->callback = $callback;
        $this->onchange = $onchange;

        parent::__construct($label);
    }
}
