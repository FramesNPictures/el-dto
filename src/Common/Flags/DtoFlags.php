<?php

namespace Fnp\Dto\Common\Flags;

use Fnp\Dto\Flag\FlagModel;
use ReflectionProperty;

class DtoFlags extends FlagModel
{
    const TO_ARRAY_INCLUDE_PUBLIC             = 0b000000000001; // Fill only public properties
    const TO_ARRAY_INCLUDE_PROTECTED          = 0b000000000010; // Fill only protected properties
    const TO_ARRAY_INCLUDE_PRIVATE            = 0b000000000100; // Fill only private properties
    const TO_ARRAY_EXCLUDE_NULLS              = 0b000000001000; // Exclude values with NULL
    const TO_ARRAY_DONT_SERIALIZE_OBJECTS     = 0b000000010000; // Do Not Serialize objects
    const TO_ARRAY_SERIALIZE_STRING_PROVIDERS = 0b000000100000; // Serialize objects with __toString
    const TO_ARRAY_PREFER_STRING_PROVIDERS    = 0b000001000000; // Prefer String Providers over Object Serialization
    const FILL_PUBLIC                         = 0b000010000000; // Fill only public properties
    const FILL_PROTECTED                      = 0b000100000000; // Fill only protected properties
    const FILL_PRIVATE                        = 0b001000000000; // Fill only private properties
    const FILL_STRICT_PROPERTIES              = 0b010000000000; // Strict property matching (no Camel <=> Snake)

    /**
     * Reflection options based on the flags
     *
     * @return int|mixed
     */
    public function fillReflectionOptions()
    {
        $options = 0;

        if ($this->has(self::FILL_PUBLIC))
            $options += ReflectionProperty::IS_PUBLIC;

        if ($this->has(self::FILL_PROTECTED))
            $options += ReflectionProperty::IS_PROTECTED;

        if ($this->has(self::FILL_PRIVATE))
            $options += ReflectionProperty::IS_PRIVATE;

        if ($options < 1)
            $options = ReflectionProperty::IS_PUBLIC +
                       ReflectionProperty::IS_PROTECTED +
                       ReflectionProperty::IS_PRIVATE;

        return $options;
    }

    /**
     * Should the property matching be string, meaining
     * no Camel Case <=> Snake Case conversion.
     *
     * @return bool
     */
    public function strictProperties()
    {
        return $this->has(self::FILL_STRICT_PROPERTIES);
    }

    /**
     * Reflection options based on the flags
     *
     * @return int|mixed
     */
    public function toArrayReflectionOptions()
    {
        $options = 0;

        if ($this->has(self::TO_ARRAY_INCLUDE_PUBLIC))
            $options += ReflectionProperty::IS_PUBLIC;

        if ($this->has(self::TO_ARRAY_INCLUDE_PROTECTED))
            $options += ReflectionProperty::IS_PROTECTED;

        if ($this->has(self::TO_ARRAY_INCLUDE_PRIVATE))
            $options += ReflectionProperty::IS_PRIVATE;

        if ($options < 1)
            $options = ReflectionProperty::IS_PUBLIC +
                       ReflectionProperty::IS_PROTECTED;

        return $options;
    }

    public function serializeObjects()
    {
        return $this->not(self::TO_ARRAY_DONT_SERIALIZE_OBJECTS);
    }

    public function serializeStringProviders()
    {
        return $this->has(self::TO_ARRAY_SERIALIZE_STRING_PROVIDERS);
    }

    public function excludeNulls()
    {
        return $this->has(self::TO_ARRAY_EXCLUDE_NULLS);
    }

    public function preferStringProviders()
    {
        return $this->has(self::TO_ARRAY_PREFER_STRING_PROVIDERS);
    }
}