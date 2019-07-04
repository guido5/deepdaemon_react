<?php
class Member
{
    // database connection and table name
    private $conn;
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
        $this->conn = $db;
    }

    function read_all($status)
    {
        // select all query
        $query =   "SELECT member.id, CONCAT( member.name, ' ', member.lastname) AS name,
                            member.linkedin, member.email, member.short_desc, member.photo_filename,
                            GROUP_CONCAT( grade.type ) AS grade,
                            GROUP_CONCAT( career.short_name ) AS career,
                            GROUP_CONCAT( school.short_name ) AS school
                    FROM member
                    left JOIN grade  ON grade.id_member = member.id
                    left JOIN career ON career.id = grade.id_career
                    left JOIN school ON school.id = grade.id_school
                    WHERE member.status LIKE '$status'
                    GROUP BY member.id
                    ORDER BY member.lastname;";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        $this->parse($stmt);
    }

    function read($id)
    {
        // select all query
        $query =   "SELECT member.id, CONCAT( member.name, ' ', member.lastname) AS name,
                            member.linkedin, member.email, member.short_desc, member.photo_filename,
                            GROUP_CONCAT( grade.type ) AS grade,
                            GROUP_CONCAT( career.short_name ) AS career,
                            GROUP_CONCAT( school.short_name ) AS school
                    FROM member
                    left JOIN grade  ON grade.id_member = member.id
                    left JOIN career ON career.id = grade.id_career
                    left JOIN school ON school.id = grade.id_school
                    WHERE member.id = $id
                    GROUP BY member.id;";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        $this->parse($stmt);
    }

    private function parse ($stmt)
    {
        $num = $stmt->rowCount();
        // check if more than 0 record found
        if($num>0)
        {
            // members array
            $members_arr=array();
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $member_item=array(
                    "id"=> $id,
                    "name" => $name,
                    "linkedin" => $linkedin,
                    "email" => $email,
                    "short_desc" => $short_desc,
                    "photo_filename" => $photo_filename,
                    "grade" => explode(",",$grade),
                    "career" => explode(",", $career),
                    "school" => explode(",", $school)
                );
                array_push($members_arr,$member_item);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show members data in json format
            echo json_encode($members_arr);
        }
        else
        {
            // set response code - 404 Not found
            http_response_code(404);
            // tell the user no members found
            echo json_encode(
                array("message" => "No members found.")
            );
        }
    }
}