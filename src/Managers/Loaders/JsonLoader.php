<?php

namespace BigF\Managers\Loaders;

class JsonLoader extends LoaderAbstract
{
    public function load()
    {
        $data = file_get_contents($this->getPath());

        return json_decode($data, true);
    }
}