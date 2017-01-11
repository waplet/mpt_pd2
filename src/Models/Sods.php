<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Sods
{
    public $table = 'sods';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    public static function save($foul)
    {
        $model = self::prepare();

        $where = [
            'laiks' => $foul['laiks'],
            'spele_key' => $foul['spele_key'],
            'speletajs_key' => $foul['speletajs_key']
        ];

        if ($model->where($where)->exists()) {
            $gameId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $gameId = $model->insertGetId($foul);
        }

        return $gameId;
    }
}