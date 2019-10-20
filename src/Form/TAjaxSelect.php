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

    /** @var callable */
    private $onchange;

    /** @var \Nette\Caching\Cache */
    private $storage;

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function setOnchange(callable $onchange) : self
    {
        $this->onchange = $onchange;
        return $this;
    }

    public function setCacheStorage(\Nette\Caching\Cache $storage) : self
    {
        $this->storage = $storage;
        return $this;
    }

    public function getControl(): \Nette\Utils\Html
    {
        $this->initiateItems();

        $attrs = [];
        $control = parent::getControl();

        if ($this->callback) {
            $attrs['data-ajaxselect'] = $this->getForm()->getPresenter()->link(
                $this->lookupPath(Presenter::class) . IComponent::NAME_SEPARATOR . self::CALLBACK_SIGNAL_NAME . '!'
            );
        }

        if ($this->onchange) {
            $attrs['data-onchange'] = $this->getForm()->getPresenter()->link(
                $this->lookupPath(Presenter::class) . IComponent::NAME_SEPARATOR . self::ONCHANGE_SIGNAL_NAME . '!'
            );
        }

        $control->addAttributes($attrs);
        return $control;
    }

    public function setValue($value): void
    {
        $this->initiateItems($value);

        parent::setValue($value);
    }

    public function signalReceived(string $signal): void
    {
        $presenter = $this->lookup(Presenter::class);

        if (!$presenter->isAjax() || $this->isDisabled()) {
            return;
        }

        switch ($signal) {
            case self::CALLBACK_SIGNAL_NAME:
                $presenter->sendJson($this->getData($presenter->getParameter('q')));
                break;
            case self::ONCHANGE_SIGNAL_NAME:
                $this->fireOnchange($presenter->getParameter('s'));
                break;
        }
    }

    /**
     * @param string $query
     * @param array|int|null $default
     * @return array
     */
    private function getData(string $query = '', $default = null): array
    {
        if ($this->callback === null) {
            throw new \Nette\InvalidStateException('Callback for "' . $this->getHtmlId() . '" is not set.');
        }

        if ($this->storage instanceof \Nette\Caching\Cache) {
            $cacheKey = $this->getHtmlId() . '_' . $query . '_' . (\is_array($default) ? \implode(',', $default) : $default);
            $result = $this->storage->load($cacheKey);

            if (\is_array($result) && !empty($result)) {
                return $result;
            }
        }

        $data = \call_user_func($this->callback, $query, $default);

        if (!\is_array($data)) {
            throw new \Nette\InvalidStateException('Callback for "' . $this->getHtmlId() . '" must return array.');
        }

        if ($this->storage instanceof \Nette\Caching\Cache) {
            $this->storage->save($cacheKey, $data);
        }

        return $data;
    }

    private function initiateItems($value = null): void
    {
        $value = $value ?? $this->value;

        if (\in_array($value, [null, '', []], true)) {
            if (\count($this->items) > 0) {
                return;
            }

            $this->items = $this->getData();
        } else {
            $this->items = $this->getData('', $value);
        }
    }

    private function fireOnchange($selected = null) : void
    {
        if ($this->onchange === null) {
            throw new \Nette\InvalidStateException('Onchange for "' . $this->getHtmlId() . '" is not set.');
        }

        \call_user_func($this->onchange, $selected);
    }
}
