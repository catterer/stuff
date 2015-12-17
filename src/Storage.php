<?php
class Storage extends SQLite3 {
    function __construct($filename) {
        $this->open($filename);
        $this->exec('CREATE TABLE IF NOT EXISTS users(
                    _id INTEGER PRIMARY KEY);');
        $this->exec('CREATE TABLE IF NOT EXISTS messages(
                    _id INTEGER PRIMARY KEY,
                    fromUserId INTEGER,
                    toUserId INTEGER,
                    time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    body TEXT,
                    FOREIGN KEY(fromUserId) REFERENCES users(_id),
                    FOREIGN KEY(toUserId) REFERENCES users(_id));');
    }

    function getUser($id) {
        $stmt = $this->prepare('SELECT _id FROM users WHERE _id=:id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $id = $result->fetchArray()["_id"];
        $result->finalize();
        return $id;
    }

    function createUser() {
        $stmt = $this->prepare('INSERT INTO users(_id) VALUES(NULL)');
        $result = $stmt->execute();
        $result->finalize();
        return $this->lastInsertRowID();
    }

    function createMessage($f, $t, $b) {
        $stmt = $this->prepare('INSERT INTO
                messages(fromUserId, toUserId, body)
                VALUES(:fromUserId, :toUserId, :body)');
        $stmt->bindValue(':fromUserId', $f, SQLITE3_INTEGER);
        $stmt->bindValue(':toUserId', $t, SQLITE3_INTEGER);
        $stmt->bindValue(':body', $b, SQLITE3_TEXT);
        $result = $stmt->execute();
        $result->finalize();
        return $this->lastInsertRowID();
    }

    function oldestMessage($uid) {
        $stmt = $this->prepare('SELECT
                _id, time, fromUserId, toUserId, body
                FROM messages
                WHERE (toUserId=:id)
                LIMIT 1');
        $stmt->bindValue(':id', $uid, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray();
    }

    function dropMessage($id) {
        $stmt = $this->prepare('DELETE FROM messages WHERE _id=:id;');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
    }
}
?>
