<?php

namespace Fnp\Dto\Config;

use Fnp\Dto\Common\Traits\DtoFill;
use Fnp\Dto\Common\Helper\Obj;

class ConfigModel
{
    use DtoFill;

    protected static $_config = [];

    public function __construct()
    {
        $className = get_called_class();

        if (isset(self::$_config[ $className ])) {
            $config = self::$_config[ $className ];
            $this->fill($config);

            return;
        }

        $config = $this->build();

        if ($method = Obj::methodExists($this, 'get', 'config')) {
            $config = array_merge($config, $this->$method());
        }

        self::$_config[ $className ] = $config;

        $this->fill($config);
    }

    public function build()
    {
        try {
            $reflection = new \ReflectionClass($this);
        } catch (\ReflectionException $e) {
            return [];
        }

        $variables = $reflection->getProperties();
        $build     = [];

        /** @var \ReflectionProperty $var */
        foreach ($variables as $variable) {

            $variable->setAccessible(TRUE);

            $varName    = $variable->getName();
            $varValue   = $variable->getValue($this);
            $envKey     = NULL;
            $envDefault = NULL;
            $envValue   = NULL;

            if (!$varValue) {
                continue;
            }

            if (preg_match('/(.*)=(.*)$/u', $varValue, $match)) {
                $envKey     = $match[1];
                $envDefault = $match[2];
            }

            if (!$envKey) {
                $envKey = $varValue;
            }

            if (isset($_ENV[ $envKey ])) {
                $envValue = $_ENV[ $envKey ];
            } else {
                $envValue = $envDefault;
            }

            $variable->setValue($this, $envValue);

            $build[ $varName ] = $envValue;

        }

        return $build;
    }
}