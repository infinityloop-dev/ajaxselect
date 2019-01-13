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

	public function getControl() : \Nette\Utils\Html
	{
        if (\count($this->items) === 0) {
            if (!\in_array($this->value, [null, '', []], true)) {
                $this->items = $this->getData('', $this->value);
            }
            else {
                $this->items = $this->getData();
            }
        }

		$attrs = [];
		$control = parent::getControl();

		$attrs['data-ajaxselect'] = $this->getForm()->getPresenter()->link(
		    $this->lookupPath(Presenter::class) . IComponent::NAME_SEPARATOR . self::SIGNAL_NAME . '!'
        );

		$control->addAttributes($attrs);
		return $control;
	}

    /**
     * @param string $query
     * @param array|int $default
     * @return array
     */
	private function getData(string $query = '', $default = null) : array
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

    /**
     * @param mixed $value
     */
    public function setValue($value) : void
    {
        if (!\array_key_exists($value, $this->items)) {
            if (!\in_array($value, [null, '', []], true)) {
                $this->items = $this->getData('', $value);
            }
            else if (!\in_array($this->value, [null, '', []], true)) {
                $this->items = $this->getData('', $this->value);
            }
            else {
                $this->items = $this->getData();
            }
        }

        parent::setValue($value);
    }

	public function setCallback(callable $callback) : self
	{
		$this->callback = $callback;
		return $this;
	}

    /**
     * @param string $signal
     */
    public function signalReceived($signal) : void
    {
        $presenter = $this->lookup(Presenter::class);

        if ($signal !== self::SIGNAL_NAME || !$presenter->isAjax() || $this->isDisabled()) {
            return;
        }

        $query = $presenter->getParameter('q');

        $data = $this->getData($query);

        $presenter->sendJson($data);
    }
}
