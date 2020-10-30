<?php
class Project
{
    // database connection and table name
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function read_all()
    {
        // select all query
        $query = "SELECT *
                    FROM project
                    ORDER BY name;";

        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        $this->parseProjects($stmt);
    }

    public function read_by_state($state)
    {
        // select all query
        $query = "SELECT *
                    FROM project
                    WHERE state='$state'
                    ORDER BY name;";
        // prepare query statement
        $stmt = $this->db->getConnection()->prepare($query);
        // execute query
        $stmt->execute();
        
        $this->parseProjects($stmt);
    }

    public function read($id)
    {
        $query = "SELECT p.id, p.name, p.`desc`, p.impact, p.state, p.modal_type, p.modal_media, p.link,
                    GROUP_CONCAT(DISTINCT CONCAT(member.name, ' ', member.lastname) ORDER BY member.lastname) AS members,
                    GROUP_CONCAT(DISTINCT tech.name ORDER BY tech.name) as tech_short,
                    GROUP_CONCAT(DISTINCT tech.desc ORDER BY tech.name) as tech_long
                  FROM project p
                  LEFT JOIN project_tech ON project_tech.id_project = p.id
                  LEFT JOIN tech ON tech.id = project_tech.id_tech
                  LEFT JOIN project_member ON project_member.id_project = p.id
                  LEFT JOIN member ON project_member.id_member = member.id
                  WHERE p.id = $id
                  GROUP BY p.id
                  ORDER BY p.name;";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $this->parseProjectById($stmt);
    }


    public function read_complete()
    {
        $query = "SELECT project.*, 
                GROUP_CONCAT(DISTINCT tech.id) as techId, 
                GROUP_CONCAT(DISTINCT tech.desc) as techDesc, 
                GROUP_CONCAT(DISTINCT member.id) as memberId, 
                GROUP_CONCAT(DISTINCT CONCAT(member.name, member.lastname)) as memberName
                FROM project 
                LEFT JOIN project_tech ON project_tech.id_project = project.id 
                LEFT JOIN tech ON project_tech.id_tech = tech.id 
                LEFT JOIN project_member ON project_member.id_project = project.id 
                LEFT JOIN member on project_member.id_member = member.id 
                GROUP BY project.id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $this->parseAllData($stmt);
    }

    public function create($name, $desc, $state, $impact, $modal_type, $link) {
        try {
            $front_img = $_FILES['front_img']['name'];
            $modal_media = $_FILES['modal_media']['name'];
            $query = "INSERT INTO project(`name`,`desc`,`state`,`impact`,`front_img`,`modal_media`,`modal_type`,`link`) VALUES (NULLIF('$name',''), NULLIF('$desc',''), '$state', NULLIF('$impact',''), NULLIF('$front_img',''), NULLIF('$modal_media',''),'$modal_type',NULLIF('$link',''));";
            $this->db->getConnection()->exec($query);
            copy($_FILES['front_img']['tmp_name'], SystemInfo::$path_project.$_FILES['front_img']['name']);
            copy($_FILES['modal_media']['tmp_name'], SystemInfo::$path_project.$_FILES['modal_media']['name']);
            echo "Se guardaron los datos";
        } catch (PDOException $e) {
            echo "Error no se guardaron los datos.";
        }
    }

    public function update($id, $name, $desc, $state, $impact, $modal_type, $link) {
        try {
            $front_img = $_FILES['front_img']['name'];
            $modal_media = $_FILES['modal_media']['name'];
            $query = "UPDATE project SET `name`=NULLIF('$name',''), `desc`=NULLIF('$desc',''), `state`='$state', `impact`=NULLIF('$impact',''), `front_img`=NULLIF('$front_img',''), `modal_media`=NULLIF('$modal_media',''), `modal_type`='$modal_type', `link`=NULLIF('$link','') WHERE `id`='$id';";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute();
            copy($_FILES['front_img']['tmp_name'], SystemInfo::$path_project.$_FILES['front_img']['name']);
            copy($_FILES['modal_media']['tmp_name'], SystemInfo::$path_project.$_FILES['modal_media']['name']);
            echo "Se actualizaron los datos";
        } catch (PDOException $e) {
            echo "Error no se guardaron los datos.";
        }  
    }

    public function deleteFromDatabase($id) {
        try{
            $query = "DELETE FROM project WHERE `id`='$id';";
            $this->db->getConnection()->exec($query);
        } catch (PDOException $e) {
            echo "Error no se elimino los datos.";
        } 
    }

    private function parseProjectById($stmt)
    {
        $num = $stmt->rowCount();
        if ($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $project = array(
                    "id" => $id,
                    "name" => $name,
                    "desc" => $desc,
                    "state" => $state,
                    "impact" => $impact,
                    "members" => is_null($members) ? [] : explode(",", $members),
                    "tech_short" => is_null($tech_short) ? [] : explode(",", $tech_short),
                    "tech_long" => is_null($tech_long) ? [] : explode(",", $tech_long),
                    "modal_media" => $modal_media,
                    "modal_type" => $modal_type,
                    "link" => $link
                );
            }
            http_response_code(200);
            echo json_encode($project);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No projects found.")
            );
        }
    }

    private function parseAllData($stmt)
    {
        $num = $stmt->rowCount();
        if ($num > 0) {
            $projects = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $project = array(
                    "id" => $id,
                    "name" => $name,
                    "desc" => $desc,
                    "state" => $state,
                    "impact" => $impact,
                    "front_img" => $front_img,
                    "modal_media" => $modal_media,
                    "modal_type" => $modal_type,
                    "link" => $link,
                    "techId" => is_null($techId) ? null : explode(",", $techId),
                    "techDesc" => is_null($techDesc) ? null : explode(",", $techDesc),
                    "memberId" => is_null($memberId) ? null : explode(",", $memberId),
                    "memberName" => is_null($memberName) ? null : explode(",", $memberName),
                );
                array_push($projects, $project);
            }
            http_response_code(200);
            echo json_encode($projects);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No projects found.")
            );
        }
    }

    private function parseProjects($stmt)
    {
        $num = $stmt->rowCount();
        if ($num > 0) {
            $projects = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $project = array(
                    "id" => $id,
                    "name" => $name,
                    "desc" => $desc,
                    "state" => $state,
                    "impact" => $impact,
                    "front_img" => $front_img,
                    "modal_media" => $modal_media,
                    "modal_type" => $modal_type,
                    "link" => $link,
                );
                array_push($projects, $project);
            }
            http_response_code(200);
            echo json_encode($projects);
        } else {
            http_response_code(404);
            echo json_encode(
                array("message" => "No projects found.")
            );
        }
    }
}
