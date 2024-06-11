<?php

if (!empty($_GET['qry'])) {
    // includes
    require_once "../db/db_conn.php";
    require_once "config.php";

    // query
    $qry = $conn->real_escape_string(htmlentities($_GET['qry']));
    $qry = "%$qry%";

    // Sanitize the query string
    $output = "";
    $stmt = $conn->prepare("SELECT upload_title,upload_pathid FROM uploads WHERE upload_title LIKE ?");
    $stmt->bind_param("s", $qry);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows) {
        while ($row = $res->fetch_assoc()) {
            $title = $row['upload_title'];
            $fileId = $row['upload_pathid'];
            $output .= "<a class='d-flex gap-3 px-3 py-2 text-dark' href='{$baseUrl}player.php?fileId=$fileId'>
                            <i class='fa-solid fa-search h-100'></i>
                            <small class='text-secondary'>$title</small>
                        </a>";
        }
    } else {
        $output = "No results found";
    }
    echo $output;
}

$conn->close();
