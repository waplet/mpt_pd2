<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Maina
{
    public $table = 'maina';

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function prepare()
    {
        $table = (new self)->table;

        return Manager::table($table);
    }

    public static function save($substitution)
    {
        $model = self::prepare();

        $where = [
            // 'laiks' => $foul['laiks'],
            'spele_key' => $substitution['spele_key'],
            'komanda_key' => $substitution['komanda_key'],
            'speletajs_nost_key' => $substitution['speletajs_nost_key'],
            'speletajs_uz_key' => $substitution['speletajs_uz_key']
        ];

        if ($model->where($where)->exists()) {
            $substitutionId = $model->select('id')
                ->where($where)
                ->value('id');
        } else {
            $substitutionId = $model->insertGetId($substitution);
        }

        return $substitutionId;
    }
}