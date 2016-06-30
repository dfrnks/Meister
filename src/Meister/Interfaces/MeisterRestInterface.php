<?php

namespace Meister\Meister\interfaces;

interface MeisterRestInterface{

    /**
     * @param $id
     * @return array
     */
    public function get($id);

    /**
     * @return array
     */
    public function post();

    /**
     * @param $id
     * @return array
     */
    public function put($id);

    /**
     * @param $id
     * @return array
     */
    public function delete($id);
}