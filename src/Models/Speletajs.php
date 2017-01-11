<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Speletajs
{
    public $table = 'speletajs';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    /**
     * @param $player
     * @return int
     */
    public static function save($player)
    {
        $model = self::prepare();

        $where = [
            'nr' => $player['nr'],
            'komanda_key' => $player['komanda_key']
        ];

        if ($model->where($where)->exists()) {
            $playerId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $playerId = $model->insertGetId($player);
        }

        return $playerId;
    }
}