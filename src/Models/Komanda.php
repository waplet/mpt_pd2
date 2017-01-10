<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Komanda
{
    public $table = 'komanda';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    /**
     * @param $team
     * @return int
     */
    public static function save($team)
    {
        $model = self::prepare();

        if ($model->where($team['data'])->exists()) {
            $teamId = $model->select('id')
                ->where($team['data'])
                ->value('id');
        } else {
            $teamId = $model->insertGetId($team['data']);
        }

        return $teamId;
    }
}