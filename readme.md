SQL task based on MySQL v5.7.24
    1) Get all statuses, not repeating, alphabetically ordered.
    
        SELECT DISTINCT status FROM test.tasks ORDER BY status;

    2) Get the count of all tasks in each project, order by tasks count descending.
    
            SELECT 	projects.NAME, COUNT(tasks.project_id)
            FROM 		projects
            LEFT JOIN tasks ON tasks.project_id = projects.id
                            
            GROUP BY projects.id
            ORDER BY COUNT(tasks.project_id) DESC;

    3) Get the count of all tasks in each project, order by project name.

            SELECT 	projects.NAME, COUNT(tasks.project_id)
            FROM 	projects
            LEFT JOIN tasks ON tasks.project_id = projects.id          
            GROUP BY projects.id
            ORDER BY projects.NAME;

    4) Get rhe tasks for all projects having the name beginning with "N" letter.

            SELECT 	tasks.name
            FROM 	projects
            LEFT JOIN tasks ON tasks.project_id = projects.id
            WHERE	projects.NAME LIKE 'N%';

   5) Get the list of all projects containing the "a" letter in the middle of the name, and show the tasks count near each project.

            SELECT 	projects.NAME, COUNT(tasks.project_id)
            FROM 	projects
            LEFT JOIN tasks ON tasks.project_id = projects.id
            WHERE projects.NAME LIKE '_%a_%'
            GROUP BY projects.id;

    6) Get the list of tasks with duplicate names. Order alphabeticaly.

            SELECT 	NAME
            FROM tasks
            GROUP BY NAME
            HAVING COUNT(NAME)>1
            ORDER BY NAME ASC;

    7) Get list of tasks having several exact matches of both name and status, from project "Garage". Order by matches count.

            SELECT 	t.NAME, amount , projects.NAME                
            FROM
                    (  
                            SELECT NAME, COUNT(id) AS amount, MAX(project_id) AS pid
                            FROM 	tasks                
                            WHERE NAME = STATUS
                            GROUP BY name
                    ) AS t
            JOIN projects ON pid = projects.id
            WHERE projects.NAME = 'Garage'
            ORDER BY amount DESC;

    8) Get list of project names having more than 10 tasks in status "completed". Order by "project_id".

        SELECT  projects.NAME                
            FROM
                    (  
                            SELECT project_id
                            FROM tasks	                
                            WHERE STATUS = 'complete'
                            GROUP BY project_id                
                            HAVING COUNT(id) > 10
                    ) AS t
            JOIN projects ON t.project_id = projects.id
            ORDER BY t.project_id;
