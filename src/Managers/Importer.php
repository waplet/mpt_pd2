<?php

namespace BigF\Managers;

use BigF\Models\Komanda;
use BigF\Models\Maina;
use BigF\Models\Pamatsastavs;
use BigF\Models\Piespele;
use BigF\Models\Sods;
use BigF\Models\Spele;
use BigF\Models\Speletajs;
use BigF\Models\Tiesnesis;
use BigF\Models\Varti;

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

        $players = [];
        foreach ($teams as $team) {
            $players[$team['id']] = $this->getPlayerData($team);
        }
        $players = $this->savePlayerData($players);

        $basePlayers = [];
        foreach ($teams as $team) {
            $basePlayers[$team['id']] = $this->getBasePlayerData($team, $players[$team['id']], $game['id']);
        }
        $basePlayers = $this->saveBasePlayerData($basePlayers);

        $fouls = [];
        foreach ($teams as $team) {
            $fouls[$team['id']] = $this->getFoulData($team, $players[$team['id']], $game['id']);
        }
        $fouls = $this->saveFoulData($fouls);

        $substitutions = [];
        foreach ($teams as $team) {
            $substitutions[$team['id']] = $this->getSubstitutionData($team, $players[$team['id']], $game['id']);
        }
        $substitutions = $this->saveSubstitutionData($substitutions);

        $goals = [];
        foreach ($teams as $team) {
            $goals[$team['id']] = $this->getGoalData($team, $players[$team['id']], $game['id']);
        }
        $goals = $this->saveGoalData($goals);

        $passes = [];
        foreach ($goals as $teamId => $teamGoals) {
            foreach($teamGoals as $goal) {
                $passes[$goal['id']] = $this->getPassData($goal, $players[$teamId]);
            }
        }
        $passes = $this->savePassData($passes);
    }

    protected function getGameData(array $data, $referees, $teams)
    {
        $game = [
            'data' => [],
            'raw' => [],
            'id' => 0,
        ];

        $gm = $data['Spele'];
        $game['data']['laiks'] = date('Y-m-d', strtotime($gm['Laiks']));
        $game['data']['skatitaji'] = $gm['Skatitaji'] ?: 0;
        $game['data']['vieta'] = $gm['Vieta'];
        $game['data']['vecakais_tiesnesis_key'] = $referees['main']['id'];

        foreach ($referees['line'] as $id => $lineJudge) {
            $game['data']['lt' . ($id + 1) . '_key'] = $lineJudge['id'];
        }

        foreach ($teams as $id => $team) {
            $game['data']['komanda' . ($id + 1) . '_key'] = $team['id'];
        }

        $game['raw'] = $data['Spele'];

        return $game;
    }

    protected function saveGameData($game)
    {
        $game['id'] = Spele::save($game['data']);

        return $game;
    }

    protected function getTeamData($data)
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

    protected function saveTeamData($teams)
    {
        foreach ($teams as &$team) {
            $team['id'] = Komanda::save($team['data']);
        }

        return $teams;
    }

    protected function getRefereeData($data)
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

    protected function saveRefereeData($referees)
    {
        $referees['main']['id'] = Tiesnesis::save($referees['main']['data']);

        foreach ($referees['line'] as &$lineJudge) {
            $lineJudge['id'] = Tiesnesis::save($lineJudge['data']);
        }

        return $referees;
    }

    protected function getPlayerData($team)
    {
        $teamPlayers = $team['raw']['Speletaji']['Speletajs'];

        $players = [];

        foreach ($teamPlayers as $player) {
            $players[] = [
                'data' => [
                    'uzvards' => $player['Uzvards'],
                    'vards' => $player['Vards'],
                    'loma' => $player['Loma'],
                    'nr' => $player['Nr'],
                    'komanda_key' => $team['id'],
                ],
                'raw' => $player,
                'id' => 0,
            ];
        }

        return $players;
    }

    protected function savePlayerData($teamPlayers)
    {
        foreach ($teamPlayers as $teamKey => &$players) {
            foreach ($players as &$player) {
                $player['id'] = Speletajs::save($player['data']);
            }
        }

        return $teamPlayers;
    }

    protected function getFoulData($team, $teamPlayers, $gameId)
    {
        $fouls = [];

        $preFouls = $team['raw']['Sodi']['Sods'];

        // The fouls may not be arrayed....
        if (array_key_exists('Laiks', $preFouls)) {
            $preFouls = [$preFouls];
        }

        foreach ($preFouls as $foul) {
            $fouls[] = [
                'data' => [
                    'laiks' => $this->getHourTime($foul['Laiks']),
                    'spele_key' => $gameId,
                    'komanda_key' => $team['id'],
                    'speletajs_key' => $this->getPlayerId($foul['Nr'], $teamPlayers)
                ],
                'raw' => $foul,
                'id' => 0,
            ];
        }

        return $fouls;
    }

    private function getPlayerId($playerNr, $teamPlayers)
    {
        $player = array_filter($teamPlayers, function ($player) use ($playerNr) {
            return $player['data']['nr']  == $playerNr;
        });
        $player = array_pop($player);

        return $player['id'];
    }

    protected function saveFoulData($fouls)
    {
        foreach ($fouls as &$teamFouls) {
            foreach ($teamFouls as &$foul) {
                $foul['id'] = Sods::save($foul['data']);
            }
        }

        return $fouls;
    }

    protected function getSubstitutionData($team, $teamPlayers, $gameId)
    {
        $substitutions = [];

        $preSubstitutions = $team['raw']['Mainas']['Maina'];

        if (array_key_exists('Laiks', $preSubstitutions)) {
            $preSubstitutions = [$preSubstitutions];
        }

        foreach ($preSubstitutions as $substitution) {
            $substitutions[] = [
                'data' => [
                    'laiks' => $this->getHourTime($substitution['Laiks']),
                    'spele_key' => $gameId,
                    'komanda_key' => $team['id'],
                    'speletajs_nost_key' => $this->getPlayerId($substitution['Nr1'], $teamPlayers),
                    'speletajs_uz_key' => $this->getPlayerId($substitution['Nr2'], $teamPlayers)
                ],
                'raw' => $substitution,
                'id' => 0,
            ];
        }

        return $substitutions;
    }

    protected function saveSubstitutionData($substitutions)
    {
        foreach ($substitutions as &$teamSubstitutions) {
            foreach ($teamSubstitutions as &$substitution) {
                $substitution['id'] = Maina::save($substitution['data']);
            }
        }

        return $substitutions;
    }

    protected function getGoalData($team, $teamPlayers, $gameId)
    {
        $goals = [];

        $preGoals = $team['raw']['Varti']['VG'];

        if (array_key_exists('Laiks', $preGoals)) {
            $preGoals = [$preGoals];
        }

        foreach ($preGoals as $goal) {
            $goals[] = [
                'data' => [
                    'laiks' => $this->getHourTime($goal['Laiks']),
                    'sitiens' => $goal['Sitiens'] == 'J' ? 1 : 0,
                    'spele_key' => $gameId,
                    'komanda_key' => $team['id'],
                    'speletajs_key' => $this->getPlayerId($goal['Nr'], $teamPlayers)
                ],
                'raw' => $goal,
                'id' => 0,
            ];
        }

        return $goals;
    }

    protected function saveGoalData($goals)
    {
        foreach ($goals as &$teamGoals) {
            foreach ($teamGoals as &$goal) {
                $goal['id'] = Varti::save($goal['data']);
            }
        }

        return $goals;
    }

    protected function getPassData($goal, $teamPlayers)
    {
        $passes = [];

        $prePasses = $goal['raw']['P'];
        if (array_key_exists('Nr', $prePasses)) {
            $prePasses = [$prePasses];
        }

        foreach ($prePasses as $pass) {
            $passes[] = [
                'data' => [
                    'laiks' => $goal['data']['laiks'],
                    'varti_key' => $goal['id'],
                    'speletajs_key' => $this->getPlayerId($pass['Nr'], $teamPlayers)
                ],
                'raw' => $pass,
                'id' => 0,
            ];
        }

        return $passes;
    }

    protected function savePassData($passes)
    {
        foreach ($passes as &$goalPasses) {
            foreach ($goalPasses as &$pass) {
                $pass['id'] = Piespele::save($pass['data']);
            }
        }

        return $passes;
    }

    protected function getBasePlayerData($team, $teamPlayers, $gameId)
    {
        $players = [];

        $prePlayers = $team['raw']['Pamatsastavs']['Speletajs'];

        if (array_key_exists('Nr', $prePlayers)) {
            $prePlayers = [$prePlayers];
        }

        foreach ($prePlayers as $player) {
            $players[] = [
                'data' => [
                    'spele_key' => $gameId,
                    'speletajs_key' => $this->getPlayerId($player['Nr'], $teamPlayers),
                    'komanda_key' => $team['id']
                ],
                'raw' => $player,
                'id' => 0,
            ];
        }

        return $players;
    }

    protected function saveBasePlayerData($basePlayers)
    {
        foreach ($basePlayers as &$teamBasePlayers) {
            foreach ($teamBasePlayers as &$player) {
                $player['id'] = Pamatsastavs::save($player['data']);
            }
        }

        return $basePlayers;
    }

    /**
     * 61:01 => 01:01:01
     * @param $time
     * @return string
     */
    private function getHourTime($time)
    {
        $timeExploded = array_map(function ($value) {
            return (int)$value;
        }, explode(":", $time));

        $hours = $timeExploded[0] / 60;
        $minutes = $timeExploded[0] % 60;
        $seconds = $timeExploded[1];

        return str_pad((int)$hours, 2, "0", STR_PAD_LEFT) . ":"
            . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":"
            . str_pad($seconds, 2, "0", STR_PAD_LEFT);
    }

    /**
     * 01:01:01 => 61:01
     * @param $hourTime
     * @return string
     */
    private function getMinutedTime($hourTime)
    {
        $hourTimeExploded = array_map(function ($value) {
            return (int)$value;
        }, explode(":", $hourTime));

        $minutes = $hourTimeExploded[0] * 60 + $hourTimeExploded[1];
        $seconds = $hourTimeExploded[2];

        return str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":"
            . str_pad($seconds, 2, "0", STR_PAD_LEFT);
    }
}
