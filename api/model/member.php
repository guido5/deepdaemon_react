<?php
class Member
{
    // database connection and table name
    private $db;
    // object properties
    public $id;
    public $name;
    public $lastname;
    public $linkedin;
    public $email;
    public $short_desc;
    public $long_desc;
    public $status;
    public $photo_filename;
    public $ss;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function readByStatus($status)
    {
        // select all query
        $query = "SELECT member.id, CONCAT( member.name, ' ', member.lastname) AS name,
                            member.linkedin, member.email, member.short_desc, member.photo_filename,
                            GROUP_CONCAT( career.short_name ) AS `career`,
                            GROUP_CONCAT( school.short_name ) AS `school`,
                            GROUP_CONCAT( career.name ) AS career_long,
                            GROUP_CONCAT( school.name ) AS school_long
                    FROM member
                    left JOIN grade  ON grade.id_member = member.id
                    left JOIN career ON career.id = grade.id_career
                    left JOIN school ON school.id = grade.id_school
                    WHERE member.status LIKE '$status'
                    GROUP BY member.id
                    ORDER BY member.lastname;";
        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        $this->parse($stmt);
    }

    public function readAll() {
        $query = "SELECT *
                    FROM member
                    ORDER BY status;";
        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        $this->parseList($stmt);
    }

    public function read_complete() {
        // select one by id query
        $query = "SELECT member.*, 
            GROUP_CONCAT(DISTINCT(school.school_id)) as school_id, 
            GROUP_CONCAT(DISTINCT(school.name)) as school_name, 
            GROUP_CONCAT(DISTINCT(career.career_id)) as career_id, 
            GROUP_CONCAT(DISTINCT(career.name)) as career_name, 
            GROUP_CONCAT(DISTINCT(project.project_id)) as project_id, 
            GROUP_CONCAT(DISTINCT(project.name)) as project_name
            FROM member
            LEFT JOIN (SELECT grade.id, grade.id_member, grade.id_school, grade.id_career FROM grade) as grade ON grade.id_member = member.id 
            LEFT JOIN (SELECT school.id as school_id, school.name FROM school) as school ON school.school_id = grade.id_school
            LEFT JOIN (SELECT career.id as career_id, career.name  FROM career) as career ON career.career_id = grade.id_career
            LEFT JOIN project_member ON project_member.id_member = member.id
            LEFT JOIN (SELECT project.id as project_id, project.name FROM project) as project ON project_member.id_project = project.project_id
            GROUP BY member.id";
        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        $this->parseComplete($stmt);
    }

    public function read($id)
    {
        // select one by id query
        $query = "SELECT member.id, CONCAT( member.name, ' ', member.lastname) AS name,
                            member.linkedin, member.email, member.short_desc, member.long_desc, member.status, member.photo_filename, member.ss,
                            GROUP_CONCAT( DISTINCT career.short_name ) AS `career`,
                            GROUP_CONCAT( DISTINCT career.name ) AS career_long,
                            GROUP_CONCAT( DISTINCT school.short_name ) AS `school`,
                            GROUP_CONCAT( DISTINCT school.name ) AS school_long,
                            GROUP_CONCAT( DISTINCT project.name ) AS projects
                    FROM member
                    LEFT JOIN grade  ON grade.id_member = member.id
                    LEFT JOIN career ON career.id = grade.id_career
                    LEFT JOIN school ON school.id = grade.id_school
                    LEFT JOIN project_member ON project_member.id_member = member.id
                    LEFT JOIN project ON project.id = project_member.id_project
                    WHERE member.id = $id
                    GROUP BY member.id;";
        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        $this->parse_one($stmt);
    }

    public function write($name, $lastname, $linkedin, $email, $short_desc, $long_desc, $status, $ss) {
        try {
            $file = $_FILES['photo_filename']['name'];
            $query = "INSERT INTO member(name, lastname, linkedin, email, short_desc, long_desc, status, photo_filename, ss) VALUES (NULLIF('$name',''), NULLIF('$lastname',''), NULLIF('$linkedin',''), NULLIF('$email',''), NULLIF('$short_desc',''), NULLIF('$long_desc',''), '$status', NULLIF('$file',''), $ss);";
            $this->db->getConnection()->exec($query);
            copy($_FILES['photo_filename']['tmp_name'], SystemInfo::$path.$_FILES['photo_filename']['name']);
            echo "Se guardaron los datos";
        } catch (PDOException $e) {
            echo "Error no se guardaron los datos.";
        }  
    }

    public function update($id, $name, $lastname, $linkedin, $email, $short_desc, $long_desc, $status, $ss) {
        try {
            $file = $_FILES['photo_filename']['name'];
            $query = "UPDATE member SET name=NULLIF('$name',''), lastname=NULLIF('$lastname',''), linkedin=NULLIF('$linkedin',''), email=NULLIF('$email',''), short_desc=NULLIF('$short_desc',''), long_desc=NULLIF('$long_desc',''), status='$status', photo_filename=NULLIF('$file',''), ss='$ss' WHERE id='$id';";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute();
            copy($_FILES['photo_filename']['tmp_name'], SystemInfo::$path.$_FILES['photo_filename']['name']);
            echo "Se actualizaron los datos";
        } catch (PDOException $e) {
            echo "Error no se guardaron los datos.";
        }  
    }

    public function deleteFromDatabase($id) {
        try{
            $query = "DELETE FROM member WHERE id='$id'";
            $this->db->getConnection()->exec($query);
        } catch (PDOException $e) {
            echo "Error no se elimino los datos.";
        } 
    }

    private function parse($stmt)
    {
        $num = $stmt->rowCount();
        // check if more than 0 record found
        if ($num > 0) {
            // members array
            $members_arr = array();
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
                $member_item = array(
                    "id" => $id,
                    "name" => $name,
                    "linkedin" => $linkedin,
                    "email" => $email,
                    "short_desc" => $short_desc,
                    "photo_filename" => $photo_filename,
                    "career" => is_null($career) ? [] : explode(",", $career),
                    "school" => is_null($school) ? [] : explode(",", $school),
                    "career_long" => is_null($career_long) ? [] : explode(",", $career_long),
                    "school_long" => is_null($school_long) ? [] : explode(",", $school_long),
                );
                array_push($members_arr, $member_item);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show members data in json format
            echo json_encode($members_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);
            // tell the user no members found
            echo json_encode(
                array("message" => "No members found.")
            );
        }
    }

    private function parse_one($stmt)
    {
        $num = $stmt->rowCount();
        if ($num > 0) {
            $members_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $member_item = array(
                    "id" => $id,
                    "name" => $name,
                    "linkedin" => $linkedin,
                    "email" => $email,
                    "short_desc" => $short_desc,
                    "status" => $status,
                    "long_desc" => $long_desc,
                    "photo_filename" => $photo_filename,
                    "ss" => $ss,
                    "career" => is_null($career) ? [] : explode(",", $career),
                    "school" => is_null($school) ? [] : explode(",", $school),
                    "career_long" => is_null($career_long) ? [] : explode(",", $career_long),
                    "school_long" => is_null($school_long) ? [] : explode(",", $school_long),
                    "projects" => is_null($projects) ? [] : explode(",", $projects),
                );
                $members_arr = $member_item;
            }
            http_response_code(200);
            echo json_encode($members_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No members found.")
            );
        }
    }

    private function parseList($stmt)
    {
        $num = $stmt->rowCount();
        // check if more than 0 record found
        if ($num > 0) {
            // members array
            $members_arr = array();
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
                $member_item = array(
                    "id" => $id,
                    "name" => $name,
                    "lastname" => $lastname,
                    "linkedin" => $linkedin,
                    "email" => $email,
                    "short_desc" => $short_desc,
                    "long_desc" => $long_desc,
                    "status" => $status,
                    "photo_filename" => $photo_filename,
                );
                array_push($members_arr, $member_item);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show members data in json format
            echo json_encode($members_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);
            // tell the user no members found
            echo json_encode(
                array("message" => "No members found.")
            );
        }
    }

    private function parseComplete($stmt)
    {
        $num = $stmt->rowCount();
        // check if more than 0 record found
        if ($num > 0) {
            // members array
            $members_arr = array();
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
                $member_item = array(
                    "id" => $id,
                    "name" => $name,
                    "lastname" => $lastname,
                    "linkedin" => $linkedin,
                    "email" => $email,
                    "short_desc" => $short_desc,
                    "long_desc" => $long_desc,
                    "status" => $status,
                    "photo_filename" => $photo_filename,
                    "school_id" => is_null($school_id) ? [] : explode(",", $school_id),
                    "school_name" => is_null($school_name) ? [] : explode(",", $school_name),
                    "career_id" => is_null($career_id) ? [] : explode(",", $career_id),
                    "career_name" => is_null($career_name) ? [] : explode(",", $career_name),
                    "project_id" => is_null($project_id) ? [] : explode(",", $project_id),
                    "project_name" => is_null($project_name) ? [] : explode(",", $project_name)
                );
                array_push($members_arr, $member_item);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show members data in json format
            echo json_encode($members_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);
            // tell the user no members found
            echo json_encode(
                array("message" => "No members found.")
            );
        }
    }

}