<?php

namespace BigF\Managers;

use BigF\Models\Komanda;

class Importer
{
    public function __construct(array $data)
    {
        $teams = $this->getTeamData($data);
        $teams = $this->saveTeamData($teams);
    }

    private function getGameData(array $data)
    {
        // $spele = [];
        //
        // if (array_key_exists('Spele', $data)) {
        //     $game = $data['Spele'];
        //     $spele['laiks'] = $game['Laiks'];
        //     $spele['skatitaji'] = $game['Skatitaji'] ?: 0;
        //     $spele['vieta'] = $game['Vieta'];
        //     $spele['vecakais_tiesnesis'] = $game['VT'];
        //     $spele['lt1'] = $game['T'][0];
        //     $spele['lt2'] = $game['T'][1];
        //     $spele['komanda1'] = $game['Komanda'][0]['Nosaukums'];
        //     $spele['komanda2'] = $game['Komanda'][1]['Nosaukums'];
        // }
        //
        // return $spele;
    }

    private function getTeamData($data)
    {
        $teams = [];
        foreach ($data['Spele']['Komanda'] as $team) {
            $teams[] = [
                'data' => [
                    'nosaukums' => $team['Nosaukums'],
                ],
                'raw' => $team
            ];
        }

        return $teams;
    }

    private function saveTeamData($teams)
    {
        foreach ($teams as &$team) {
            $team['id'] = Komanda::save($team);
        }

        return $teams;
    }
}
