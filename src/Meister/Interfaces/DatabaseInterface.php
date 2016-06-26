<?php

namespace Meister\Meister\Interfaces;

use Doctrine\ODM\MongoDB\DocumentManager;

interface DatabaseInterface {
    /**
     * @return DocumentManager
     */
    public function doc();
    public function insert($doc, $data);
    public function update($doc, $data);
}