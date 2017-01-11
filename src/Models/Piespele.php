<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Piespele
{
    public $table = 'piespele';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    public static function save($pass)
    {
        $model = self::prepare();

        $where = $pass;

        if ($model->where($where)->exists()) {
            $passId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $passId = $model->insertGetId($pass);
        }

        return $passId;
    }
}