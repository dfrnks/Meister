<?php

namespace Meister\Meister\Libraries;

/**
 * Class Data
 * @package libs
 */
class Data{

    /**
     * @param $object
     * @param string $format
     * @return array|string
     */
    public static function serialize($object, $format = "array") {
        $attributes = array();

        if(is_array($object)){
            foreach($object as $obj){
                $attributes[] = self::serialize($obj, $format);
            }
        }else{
            $reflectionObject = new \ReflectionObject($object);

            foreach ($reflectionObject->getProperties() as $property) {
                if($property->isStatic()){
                    continue;
                }

                if (!$property->isPublic()) {
                    $property->setAccessible(true);
                }

                $attributeValue = $property->getValue($object);

                $propertyName = $property->name;

                if(is_object($attributeValue)){
                    $attributeValue = self::serialize($attributeValue);
                }

                $attributes[$propertyName] = $attributeValue;
            }

            if($format == "json"){
                return json_encode($attributes);
            }
        }

        return $attributes;
    }

    public static function selArray($data){
        if(is_object($data)){
            $data = (array) $data;

            $data = self::selArray($data);
        }elseif(is_array($data)){
            foreach ($data as $k => $value){
                $data[$k] = self::selArray($value);
            }
        }
        return $data;
    }

    public static function uid(){
        return md5(uniqid(rand(), true));
    }

    public static function getHeader($header = null){
        $headers = apache_request_headers();

        if($header){
            if(array_key_exists($header,$headers)){
                return $headers[$header];
            }
            return false;
        }

        return $headers;
    }
}