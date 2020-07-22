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

class ResultObjectSet extends \Infinityloop\Utils\ObjectSet implements \JsonSerializable
{
    protected const INNER_CLASS = ResultObject::class;

    public static function fromArray(array $data) : \Nepttune\Form\ResultObjectSet
    {
        $objectSet = [];

        foreach ($data as $key => $value) {
            if ($value instanceof \Nepttune\Form\ResultObject) {
                $objectSet[] = $value;

                continue;
            }

            $objectSet[] = new \Nepttune\Form\ResultObject($key, $value);
        }

        return new self($objectSet);
    }

    public function jsonSerialize()
    {
        $toReturn = [];

        foreach ($this as $object) {
            $toReturn[] = $object->jsonSerialize();
        }

        return $toReturn;
    }

    public function getRawData() : array
    {
        $toReturn = [];

        foreach ($this as $object) {
            $toReturn[$object->getId()] = $object->getText();
        }

        return $toReturn;
    }

    public function getDisabled() : array
    {
        $toReturn = [];

        foreach ($this as $object) {
            if ($object->isDisabled()) {
                $toReturn[] = $object->getId();
            }
        }

        return $toReturn;
    }

    public function current() : \Nepttune\Form\ResultObject
    {
        return parent::current();
    }

    public function offsetGet($offset) : \Nepttune\Form\ResultObject
    {
        return parent::offsetGet($offset);
    }
}
