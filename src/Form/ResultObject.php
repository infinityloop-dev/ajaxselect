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

declare(strict_types = 1);

namespace Nepttune\Form;

class ResultObject implements \JsonSerializable
{
    private int $id;
    private string $text;
    private ?string $title = null;
    private bool $disabled;

    public function __construct(int $id, string $text, ?string $title = null, bool $disabled = false)
    {
        $this->id = $id;
        $this->text = $text;
        $this->title = $title;
        $this->disabled = $disabled;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function isDisabled() : bool
    {
        return $this->disabled;
    }

    public function jsonSerialize()
    {
        $toReturn = [
            'id' => $this->id,
            'text' => $this->text,
        ];

        if ($this->title !== null) {
            $toReturn['title'] = $this->title;
        }

        if ($this->disabled === true) {
            $toReturn['disabled'] = $this->disabled;
        }

        return $toReturn;
    }
}
