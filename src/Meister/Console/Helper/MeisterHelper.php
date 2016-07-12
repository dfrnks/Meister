<?php

namespace Meister\Meister\Console\Helper;

use Meister\Meister\init;
use Symfony\Component\Console\Helper\Helper;

class MeisterHelper extends Helper
{
    protected $init;
    
    public function __construct($init)
    {
        $this->init = $init;
    }

    /**
     * @return init
     */
    public function getInit()
    {
        return $this->init;
    }

    /**
     * Get the canonical name of this helper.
     *
     * @see \Symfony\Component\Console\Helper\HelperInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'init';
    }
}