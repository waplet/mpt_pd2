<?php

namespace BigF\Managers\Loaders;

class JsonLoader extends LoaderAbstract
{
    public function load()
    {
        $data = file_get_contents($this->getPath());

        $decoded = json_decode($data, true);

        if ($decoded == null) {
            $data = mb_convert_encoding($data, "UTF-8", "Windows-1252");
            $decoded = json_decode($data, true);
            // throw new \ErrorException(json_last_error_msg());
        }

        return $decoded;
    }
}