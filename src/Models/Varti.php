<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Varti
{
    public $table = 'varti';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    /**
     * @param $goal
     * @return int
     */
    public static function save($goal)
    {
        $model = self::prepare();

        $where = $goal;

        if ($model->where($where)->exists()) {
            $goalId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $goalId = $model->insertGetId($goal);
        }

        return $goalId;
    }
}