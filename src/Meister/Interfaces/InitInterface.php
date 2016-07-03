<?php

namespace Meister\Meister\Interfaces;

interface InitInterface{
    
    public function getRotas();

    public function getConfig($ambiente);

    public function getBaseDir();

    public function getCache();
}