<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Pamatsastavs
{
    public $table = 'pamatsastavs';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    /**
     * @param $basePlayer
     * @return int
     */
    public static function save($basePlayer)
    {
        $model = self::prepare();

        $where  = [
            'spele_key' => $basePlayer['spele_key'],
            'speletajs_key' => $basePlayer['speletajs_key'],
        ];

        if ($model->where($where)->exists()) {
            $teamId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $teamId = $model->insertGetId($basePlayer);
        }

        return $teamId;
    }
}