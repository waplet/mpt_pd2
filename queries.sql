SELECT
    k.id
    , k.nosaukums 'Team'
#     , count(DISTINCT s_home.id) 'Home'
#     , count(DISTINCT s_away.id) 'Away'
    , count(DISTINCT s_home.id) + count(DISTINCT s_away.id) 'Total'
    , count(DISTINCT v.id) 'Goals'
#     , count(DISTINCT v_lost_home.id) 'Goals lost home'
#     , count(DISTINCT v_lost_away.id) 'Goals lost away'
    , count(DISTINCT v_lost_home.id) + count(DISTINCT v_lost_away.id) 'Goals lost',
    , sum(IF (v_lost_home)) 'Won home'
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
GROUP BY k.nosaukums;

SELECT
    s.laiks
    , s.vieta
    , s.vecakais_tiesnesis_key
#     , count(v_home.id)
#     , count(v_away.id)
    , count(v.id)
FROM spele s
#     LEFT JOIN varti v_home ON v_home.spele_key = s.id AND s.komanda1_key = v_home.komanda_key
#     LEFT JOIN varti v_away ON v_away.spele_key = s.id AND s.komanda2_key = v_away.komanda_key
    LEFT JOIN varti v ON v.spele_key = s.id

GROUP BY s.id