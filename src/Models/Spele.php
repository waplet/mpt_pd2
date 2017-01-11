<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Spele
{
    public $table = 'spele';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    public static function save($game)
    {
        $model = self::prepare();

        $where = [
            'laiks' => $game['laiks'],
            'vieta' => $game['vieta'],
            'vecakais_tiesnesis_key' => $game['vecakais_tiesnesis_key'],
        ];

        if ($model->where($where)->exists()) {
            $gameId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $gameId = $model->insertGetId($game);
        }

        return $gameId;
    }
}