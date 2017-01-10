<?php

namespace BigF\Managers;

use BigF\Models\Komanda;
use BigF\Models\Tiesnesis;

class Importer
{
    public function __construct(array $data)
    {
        $teams = $this->getTeamData($data);
        // Populates teams with their database ID's
        $teams = $this->saveTeamData($teams);

        $referees = $this->getRefereeData($data);
        $referees = $this->saveRefereeData($referees);

        $game = $this->getGameData($data, $referees, $teams);
        $game = $this->saveGameData($game);
        die(dump($referees));
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
            $team['id'] = Komanda::save($team['data']);
        }

        return $teams;
    }

    private function getRefereeData($data)
    {
        $referees = [
            'main' => [],
            'line' => [],
        ];
        foreach ($data['Spele']['T'] as $lineJudge)
        {
            $referees['line'][] = [
                'data' => [
                    'uzvards' => $lineJudge['Uzvards'],
                    'vards' => $lineJudge['Vards']
                ],
                'raw' => $lineJudge,
                'id' => 0
            ];
        }

        $referees['main'] = [
            'data' => [
                'uzvards' => $data['Spele']['VT']['Uzvards'],
                'vards' => $data['Spele']['VT']['Vards'],
            ],
            'raw' => $data['Spele']['VT'],
            'id' => 0
        ];

        return $referees;
    }

    private function saveRefereeData($referees)
    {
        $referees['main']['id'] = Tiesnesis::save($referees['main']['data']);

        foreach ($referees['line'] as &$lineJudge) {
            $lineJudge['id'] = Tiesnesis::save($lineJudge['data']);
        }

        return $referees;
    }
}
