<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Tiesnesis
{
    public $table = 'tiesnesis';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    /**
     * @param $referee
     * @return int
     */
    public static function save($referee)
    {
        $model = self::prepare();

        if ($model->where($referee)->exists()) {
            $refereeId = $model->select('id')
                ->where($referee)
                ->value('id');
        } else {
            $refereeId = $model->insertGetId($referee);
        }

        return $refereeId;
    }
}