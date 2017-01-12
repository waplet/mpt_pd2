<?php

namespace BigF\Managers;

use BigF\Models\Komanda;
use BigF\Models\Spele;
use BigF\Models\Speletajs;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\JoinClause;

class Report
{
    public static function mainTable()
    {
        $model = Komanda::prepare();

        $wonGamesQuery = Spele::prepare()
            ->selectRaw('spele.id \'GameID\'')
            ->selectRaw('if(count(DISTINCT v_home.id) > count(DISTINCT v_away.id), spele.komanda1_key, spele.komanda2_key) \'Winner team\'')
            ->selectRaw('if (max(v_away.laiks) > \'00:59:59\' OR max(v_home.laiks) > \'00:59:59\', 1, 0) \'Papildlaiks\'')
            ->leftJoin('varti as v_home', function (JoinClause $join) {
                $join->on('v_home.spele_key', '=', 'spele.id');
                $join->on('v_home.komanda_key', '=', 'spele.komanda1_key');
            })
            ->leftJoin('varti as v_away', function (JoinClause $join) {
                $join->on('v_away.spele_key', '=', 'spele.id');
                $join->on('v_away.komanda_key', '=', 'spele.komanda2_key');
            })
            ->groupBy('spele.id')
            ->toSql();

        $lostGamesQuery = Spele::prepare()
            ->selectRaw('spele.id \'GameID\'')
            ->selectRaw('if(count(DISTINCT v_home.id) > count(DISTINCT v_away.id), spele.komanda2_key, spele.komanda1_key) \'Loser team\'')
            ->selectRaw('if (max(v_away.laiks) > \'00:59:59\' OR max(v_home.laiks) > \'00:59:59\', 1, 0) \'Papildlaiks\'')
            ->leftJoin('varti as v_home', function (JoinClause $join) {
                $join->on('v_home.spele_key', '=', 'spele.id');
                $join->on('v_home.komanda_key', '=', 'spele.komanda1_key');
            })
            ->leftJoin('varti as v_away', function (JoinClause $join) {
                $join->on('v_away.spele_key', '=', 'spele.id');
                $join->on('v_away.komanda_key', '=', 'spele.komanda2_key');
            })
            ->groupBy('spele.id')
            ->toSql();

        return $model->select('komanda.nosaukums as Team')
            ->selectRaw('count(DISTINCT s_home.id) + count(DISTINCT s_away.id) \'Games played\'')
            ->selectRaw('COUNT(DISTINCT v.id) \'Goals\'')
            ->selectRaw('count(DISTINCT v_lost_home.id) + count(DISTINCT v_lost_away.id) \'Goals lost\'')
            ->selectRaw('count(DISTINCT win.`GameID`) \'Games won\'')
            ->selectRaw('count(DISTINCT lost.`GameID`) \'Games lost\'')
            ->selectRaw('count(DISTINCT s1.id) \'Games won Main\'')
            ->selectRaw('count(DISTINCT s2.id) \'Games lost Main\'')
            ->selectRaw('count(DISTINCT s3.id) \'Games won OT\'')
            ->selectRaw('count(DISTINCT s4.id) \'Games lost OT\'')
            ->selectRaw('count(DISTINCT s1.id) * 5 + count(DISTINCT s3.id) * 3 + count(DISTINCT s4.id) * 2 + count(DISTINCT s2.id) * 1 \'Points\'')
            ->leftJoin('spele as s_home', 's_home.komanda1_key', '=', 'komanda.id')
            ->leftJoin('spele as s_away', 's_away.komanda2_key', '=', 'komanda.id')
            ->leftJoin('varti as v', 'v.komanda_key', '=', 'komanda.id')
            ->leftJoin('varti as v_lost_home', function (JoinClause $join) {
                $join->on('v_lost_home.spele_key', '=', 's_home.id');
                $join->on('v_lost_home.komanda_key', '!=', 'komanda.id');
            })
            ->leftJoin('varti as v_lost_away', function (JoinClause $join) {
                $join->on('v_lost_away.spele_key', '=', 's_away.id');
                $join->on('v_lost_away.komanda_key', '!=', 'komanda.id');
            })
            ->leftJoin(Manager::raw('(' . $wonGamesQuery . ') as win'), function (JoinClause $join) {
                $join->on('win.Winner team', '=', 'komanda.id');
            })
            ->leftJoin(Manager::raw('(' . $lostGamesQuery . ') as lost'), function (JoinClause $join) {
                $join->on('lost.Loser team', '=', 'komanda.id');
            })
            ->leftJoin('spele as s1', function (JoinClause $join) {
                $join->on('s1.id', '=', 'win.GameID');
                $join->on('win.Papildlaiks', '=', Manager::raw(0));
            })
            ->leftJoin('spele as s2', function (JoinClause $join) {
                $join->on('s2.id', '=', 'lost.GameID');
                $join->on('lost.Papildlaiks', '=', Manager::raw(0));
            })
            ->leftJoin('spele as s3', function (JoinClause $join) {
                $join->on('s3.id', '=', 'win.GameID');
                $join->on('win.Papildlaiks', '=', Manager::raw(1));
            })
            ->leftJoin('spele as s4', function (JoinClause $join) {
                $join->on('s4.id', '=', 'lost.GameID');
                $join->on('lost.Papildlaiks', '=', Manager::raw(1));
            })
            ->groupBy('komanda.nosaukums')
            ->orderBy('Points', 'DESC')
            // ->toSql()
            ->get()->toArray()
        ;
    }

    public static function top10Best()
    {
        $model = Speletajs::prepare();

        return $model->select([
                'speletajs.uzvards',
                'speletajs.vards',
                'k.nosaukums'
            ])
            ->selectRaw('count(v.id) \'Goals\'')
            ->selectRaw('count(p.id) \'Passes\'')
            ->leftJoin('varti as v', 'v.speletajs_key', '=', 'speletajs.id')
            ->leftJoin('piespele as p', 'p.speletajs_key', '=', 'speletajs.id')
            ->leftJoin('komanda as k', 'k.id', '=', 'speletajs.komanda_key')
            ->groupBy('speletajs.id')
            ->orderBy(Manager::raw('`Goals`'), 'DESC')
            ->orderBy(Manager::raw('`Passes`'), 'DESC')
            // Additional so that they does not change
            ->orderBy('speletajs.vards', 'ASC')
            ->orderBy('speletajs.uzvards', 'ASC')
            ->limit(10)
            ->get()
            ->toArray();
    }

}