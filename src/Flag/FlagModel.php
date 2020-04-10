<?php

namespace Fnp\Dto\Flag;

class FlagModel
{
    private $_flags = 0;

    public function __construct($flags)
    {
        if (is_null($flags))
            $flags = 0;

        if ($flags instanceof FlagModel)
            $flags = $flags->flags();

        if (is_array($flags))
            $flags = array_sum($flags);

        $this->_flags = $flags;
    }

    public static function make($flags = NULL)
    {
        return new static($flags);
    }

    /**
     * Add new flag(s)
     *
     * @param $flags
     *
     * @return $this
     */
    public function add($flags)
    {
        if ($flags instanceof FlagModel)
            $flags = $flags->flags();

        $this->_flags += $flags;

        return $this;
    }

    /**
     * Check if flag has been set
     *
     * @param $flags
     *
     * @return bool
     */
    public function has($flags)
    {
        if ($flags instanceof FlagModel)
            $flags = $flags->flags();

        return (bool)(($this->_flags & $flags) == $flags);
    }

    /**
     * Check if flag or all of the flags
     * are not set
     *
     * @param $flags
     *
     * @return bool
     */
    public function not(
        $flags
    ) {
        return !$this->has($flags);
    }

    /**
     * Return array of set flags
     *
     * @return array
     */
    public function flags()
    {
        return $this->_flags;
    }

    /**
     * Check if none of the flags are set
     *
     * @return bool
     */
    public function none()
    {
        return (bool)($this->_flags == 0);
    }
}