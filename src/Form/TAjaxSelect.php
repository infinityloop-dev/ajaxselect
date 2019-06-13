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

namespace Nepttune\Form;

use \Nette\Application\UI\Presenter;
use \Nette\ComponentModel\IComponent;

trait TAjaxSelect
{
    /** @var callable */
    private $callback;

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function getControl(): \Nette\Utils\Html
    {
        $this->initiateItems();

        $attrs = [];
        $control = parent::getControl();

        $attrs['data-ajaxselect'] = $this->getForm()->getPresenter()->link(
            $this->lookupPath(Presenter::class) . IComponent::NAME_SEPARATOR . self::SIGNAL_NAME . '!'
        );

        $control->addAttributes($attrs);
        return $control;
    }

    public function setValue($value): void
    {
        $this->initiateItems($value);

        parent::setValue($value);
    }

    public function getValue()
    {
        $this->initiateItems();

        return \array_key_exists($this->value, $this->items) ? $this->value : null;
    }

    public function signalReceived(string $signal): void
    {
        $presenter = $this->lookup(Presenter::class);

        if ($signal !== self::SIGNAL_NAME || !$presenter->isAjax() || $this->isDisabled()) {
            return;
        }

        $presenter->sendJson($this->getData($presenter->getParameter('q')));
    }

    private function getData(string $query = '', $default = null): array
    {
        if ($this->callback === null) {
            throw new \Nette\InvalidStateException('Callback for "' . $this->getHtmlId() . '" is not set.');
        }

        $data = \call_user_func($this->callback, $query, $default);

        if (!\is_array($data)) {
            throw new \Nette\InvalidStateException('Callback for "' . $this->getHtmlId() . '" must return array.');
        }

        return $data;
    }

    private function initiateItems($value = null): void
    {
        if (\count($this->items) > 0) {
            return;
        }

        if (!\in_array($value ?? $this->value, [null, '', []], true)) {
            $this->items = $this->getData('', $value ?? $this->value);
        } else {
            $this->items = $this->getData();
        }
    }
}
