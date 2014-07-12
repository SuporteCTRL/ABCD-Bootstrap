10 0 (v10/)
20 0 (v20/)
30 0 v30," "v40
30 0 (v30/)
30 4 (if v30:'resolucao' then 'listaweb' fi/)
30 0 (if v30:'constituicao federal' then 'listaweb' fi/)
30 0 (if v30:'constituicao estadual' then 'listaweb' fi/)
30 4 (if v30:'decreto' then 'listaweb' fi/)
30 4 (if v30:'decreto-lei' then 'listaweb' fi/)
30 4 (if v30:'instrucao' then 'listaweb' fi/)
30 4 (if v30:'lei' then 'listaweb' fi/)
30 4 (if v30:'medida' then 'listaweb' fi/)
30 4 (if v30:'norma' then 'listaweb' fi/)
30 4 (if v30:'portaria' then 'listaweb' fi/)
40 0 (v40/)
40 4 (v40/)
50 1 v50^a
50 0 'TODOS'
60 1 (v60/)
60 0 v60^c
60 0 v60^d
60 0 v60^a
70 0 (v70/)
70 4 (v70/)
80 0 v80^d,if p(v80^a) or p(v80^b) or p(v80^c) then ' de ' fi,v80^c" ",v80^b" ",v80^a
80 1 (v80/)
90 4 (v90/)
100 4 (v100/)
100 0 (v100/)
200 1 (|LINK=|V200^A/)
200 1 (v200/)
300 0 (v300/)
901 0 v901*4.2
901 0 v901*0.4
910 4 (v910/)
920 0 (v920/)
930 0 (v930/)
940 1 (v940^a,|-|v940^b/)
940 0 'demo'
950 0 (|CDG=|V950/)
950 0 v950
200 4 mhl, v200
800 1 v50^b
801 0 v30
