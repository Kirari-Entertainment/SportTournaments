<?php namespace Infrastructure;

abstract class PlainDataObject {
    public static function parseFromArray(array $data) : static {
        $parsedObject = new static;

        foreach (array_keys($data) as $key) {
            $parsedObject->$key = $data[$key];

        }

        return $parsedObject;
    }

    public static function checkIfCompliant(array $data) : bool {
        $objectPublicKeys = array_keys(get_object_vars(new static));
        return !array_diff_key(array_flip($objectPublicKeys), $data);
    }
}