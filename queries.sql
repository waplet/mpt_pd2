SELECT
    k.id
    , k.nosaukums 'Team'
    , count(DISTINCT s_home.id) + count(DISTINCT s_away.id) 'Games played'
    , count(DISTINCT v.id) 'Goals'
    , count(DISTINCT v_lost_home.id) + count(DISTINCT v_lost_away.id) 'Goals lost'
    , count(DISTINCT win.`GameID`) 'Games won'
    , count(DISTINCT lost.`GameID`) 'Games lost'
    , count(DISTINCT s1.id) 'Games won Main'
    , count(DISTINCT s2.id) 'Games lost Main'
    , count(DISTINCT s3.id) 'Games won OT'
    , count(DISTINCT s4.id) 'Games lost OT'
    , count(DISTINCT s1.id) * 5 + count(DISTINCT s3.id) * 3 + count(DISTINCT s4.id) * 2 + count(DISTINCT s2.id) * 1 'Points'
FROM komanda k
-- Games home
    LEFT JOIN spele s_home ON s_home.komanda1_key = k.id
-- Games away
    LEFT JOIN spele s_away ON s_away.komanda2_key = k.id
-- Goals won
    LEFT JOIN varti v ON v.komanda_key = k.id
-- Goals lost
    LEFT JOIN varti v_lost_home ON v_lost_home.spele_key = s_home.id AND v_lost_home.komanda_key != k.id
    LEFT JOIN varti v_lost_away ON v_lost_away.spele_key = s_away.id AND v_lost_away.komanda_key != k.id
-- Games won
    LEFT JOIN (SELECT
                   s.id 'GameID'
                   , if(count(DISTINCT v_home.id) > count(DISTINCT v_away.id), s.komanda1_key, s.komanda2_key) 'Winner team'
                   , if (max(v_away.laiks) > '00:59:59' OR max(v_home.laiks) > '00:59:59', 1, 0) 'Papildlaiks'
               FROM spele s
                   LEFT JOIN varti v_home ON v_home.spele_key = s.id AND s.komanda1_key = v_home.komanda_key
                   LEFT JOIN varti v_away ON v_away.spele_key = s.id AND s.komanda2_key = v_away.komanda_key
               GROUP BY s.id) win ON win.`Winner team` = k.id
-- Games lost
    LEFT JOIN (SELECT
                   s.id 'GameID'
                   , if(count(DISTINCT v_home.id) > count(DISTINCT v_away.id), s.komanda2_key, s.komanda1_key) 'Loser team'
                   , if (max(v_away.laiks) > '00:59:59' OR max(v_home.laiks) > '00:59:59', 1, 0) 'Papildlaiks'
               FROM spele s
                   LEFT JOIN varti v_home ON v_home.spele_key = s.id AND s.komanda1_key = v_home.komanda_key
                   LEFT JOIN varti v_away ON v_away.spele_key = s.id AND s.komanda2_key = v_away.komanda_key
               GROUP BY s.id) lost ON lost.`Loser team` = k.id
-- Main/Overtime games
    LEFT JOIN spele s1 ON s1.id = win.GameID AND win.Papildlaiks = 0
    LEFT JOIN spele s2 ON s2.id = lost.GameID AND lost.Papildlaiks = 0
    LEFT JOIN spele s3 ON s3.id = win.GameID AND win.Papildlaiks = 1
    LEFT JOIN spele s4 ON s4.id = lost.GameID AND lost.Papildlaiks = 1
GROUP BY k.nosaukums
    ORDER BY `Points` DESC
    ;

SELECT
    count(v.id) 'Goals'
    , count(p.id) 'Passes'
    , s.uzvards
    , s.vards
    , k.nosaukums
FROM speletajs s
    LEFT JOIN varti v ON v.speletajs_key = s.id
    LEFT JOIN piespele p ON p.speletajs_key = s.id
    LEFT JOIN komanda k ON k.id = s.komanda_key
GROUP BY s.id
ORDER BY `Goals` DESC, `Passes` DESC
LIMIT 10
;