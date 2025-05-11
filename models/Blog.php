<?php

class Blog {
    private $conn;
    private $table_name = "blogs";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBlogs() {
        $query = "SELECT * FROM " . $this->table_name ;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }
        return ["success" => true, "blogs" => $blogs];
    }

    public function getBlogById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteBlog($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function createBlog($title, $content, $author) {
        // Validate inputs
        if (empty($title) || empty($content) || empty($author)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($title) > 255) {
            return ['success' => false, 'error' => 'Title must be less than 255 characters'];
        }
        
        if (strlen($content) < 10) {
            return ['success' => false, 'error' => 'Content must be at least 10 characters long'];
        }

        $query = "INSERT INTO " . $this->table_name . " (title, content, author, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $title, $content, $author);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to create blog'];
        }
    }

    public function getRandomBlogs($limit = 6) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY RAND() LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }
        return ["success" => true, "blogs" => $blogs];
    }

    public function getPaginatedBlogs($page, $limit) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $this->table_name . " LIMIT ?, ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }

        // Get total count of blogs
        $totalQuery = "SELECT FOUND_ROWS() as total";
        $totalResult = $this->conn->query($totalQuery);
        $total = $totalResult->fetch_assoc()['total'];

        return [
            "success" => true,
            "blogs" => $blogs,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        ];
    }
}
?>