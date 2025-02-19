/*Jelenítse meg az évfolyamokat (pl: 2022, 2023, 2024)*/

USE schoolbook;

SELECT year FROM classes
group by year

/*Jelenítse meg az osztályokat a kiválasztott évfolyamban (pl: 11A, 11B, 11C, 12A, 12B...)*/

USE schoolbook;

SELECT code FROM classes
where year = 2023  /*$változó*/

/*Jelenítse meg a kiválasztott osztályba járó tanulókat  a tanulók neve szerint rendezve (pl 2024/11i)*/

USE schoolbook;

SELECT name FROM students
where class_id = /*$selected class*/
ORDER by name


/*Jelenítse meg az egyes osztályok átlagát.*/

USE schoolbook;

SELECT AVG(grade) FROM grades g
join students s ON s.id = g.student_id
JOIN classes c ON c.id = s.class_id
where class_id = /*$selected class*/
ORDER by name

/*Jelenítse meg az egyes tanulók átlagát.*/

USE schoolbook;

SELECT name, round(AVG(g.grade),2) FROM students s
JOIN grades g ON g.student_id = s.id
where class_id = 1/*$selected class*/
group by name
ORDER by name

/*Jelenítse meg az egyes tanulók átlagát tantárgyanként.*/

USE schoolbook;

SELECT s.name, su.name, round(AVG(g.grade),2) FROM students s
JOIN grades g ON g.student_id = s.id
JOIN subjects su ON su.id = g.subject_id
where class_id = 1/*$selected class*/
group by g.student_id, g.subject_id
ORDER by s.name

/*Jelenítse meg az egyes osztályok átlagát tantárgyanként.*/

SELECT c.year, c.code,AVG(g.grade) from grades g
JOIN students s ON s.id = g.student_id
JOIN classes c ON s.class_id = c.id
GROUP BY s.class_id

/*Jelenítse meg az iskola 10 legjobb tanulóját az elért átlaguk sorrendjében a kiválasztott évben*/

SELECT name, round(AVG(g.grade),2) as atlag FROM students s
JOIN grades g ON g.student_id = s.id
JOIN classes c ON c.id = s.class_id
where year = 2025 /*$selected year*/
group by name
ORDER by atlag DESC
LIMIT 10

/*Hozzon létre egy Hall of Fame-t, a mindenkori legjobb osztállyal és 10 legjobb tanulóval.*/


SELECT name from students 
where class_id = (SELECT c.id from grades g
                  JOIN students s ON s.id = g.student_id
                  JOIN classes c ON s.class_id = c.id
                  GROUP BY s.class_id
                  order by AVG(g.grade) DESC
                  limit 1)
LIMIT 10


SELECT c.code,c.year from students s
JOIN classes c ON c.id = s.class_id
where class_id = (SELECT c.id from grades g
                  JOIN students s ON s.id = g.student_id
                  JOIN classes c ON s.class_id = c.id
                  GROUP BY s.class_id
                  order by AVG(g.grade) DESC
                  limit 1)
LIMIT 1