<?php

namespace Fnp\Dto\Common\Flags;

use Fnp\Dto\Flag\FlagModel;
use ReflectionProperty;

class Dto extends FlagModel
{
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
        return $this->has(self::STRICT_PROPERTIES);
    }

    /**
     * Reflection options based on the flags
     *
     * @return int|mixed
     */
    public function toArrayReflectionOptions()
    {
        $options = 0;

        if ($this->has(self::INCLUDE_PUBLIC))
            $options += ReflectionProperty::IS_PUBLIC;

        if ($this->has(self::INCLUDE_PROTECTED))
            $options += ReflectionProperty::IS_PROTECTED;

        if ($this->has(self::INCLUDE_PRIVATE))
            $options += ReflectionProperty::IS_PRIVATE;

        if ($options < 1)
            $options = ReflectionProperty::IS_PUBLIC +
                ReflectionProperty::IS_PROTECTED;

        return $options;
    }

    public function serializeObjects()
    {
        return $this->not(self::DONT_SERIALIZE_OBJECTS);
    }

    public function serializeStringProviders()
    {
        return $this->has(self::SERIALIZE_STRING_PROVIDERS);
    }

    public function excludeNulls()
    {
        return $this->has(self::EXCLUDE_NULLS);
    }

    public function preferStringProviders()
    {
        return $this->has(self::PREFER_STRING_PROVIDERS);
    }
}