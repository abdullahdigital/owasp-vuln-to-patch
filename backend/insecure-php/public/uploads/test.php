if (isset($_GET['debug'])) {
    $users = [];
    $result = $db->query("SELECT * FROM users");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $row;
    }
    sendResponse([
        "status" => "debug",
        "users" => $users
    ]);
}
