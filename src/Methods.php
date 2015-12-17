<?php

function string_checker($s) {
    return is_string($s);
}

function int_checker($s) {
    return is_int($s);
}

function missingFields($httpReq, $json, $fields) {
    foreach($fields as $field => $checker) {
        if (!property_exists($json, $field)) {
            $httpReq->abort(400, "Field '$field' not specified");
            return TRUE;
        }
        if (!call_user_func($checker, $json->$field)) {
            $httpReq->abort(400, "Field '$field' has wrong type");
            return TRUE;
        }
    }
    return FALSE;
}

$methods = array(
    'sendMessage' => function($httpReq, $json, $storage) {
        if (missingFields($httpReq, $json, [
                "fromUserId" => 'int_checker',
                "toUserId" => 'int_checker',
                "body" => 'string_checker']))
            return;

        if (!$storage->getUser($json->fromUserId)) {
            $httpReq->abort(400, "No such user $json->fromUserId");
            return;
        }
        if (!$storage->getUser($json->toUserId)) {
            $httpReq->abort(400, "No such user $json->toUserId");
            return;
        }

        $storage->createMessage($json->fromUserId, $json->toUserId, $json->body);
        $httpReq->done(NULL);
    },

    'createUser' => function($httpReq, $json, $storage) {
        $reply = new stdClass();
        $reply->userId = $storage->createUser();
        $httpReq->done($reply);
    },

    'fetchMessage' => function($httpReq, $json, $storage) {
        if (missingFields($httpReq, $json, ["userId" => 'int_checker']))
            return;
        if (!$storage->getUser($json->userId)) {
            $httpReq->abort(400, "No such user $json->userId");
            return;
        }
        $res = $storage->oldestMessage($json->userId);
        if ($res["_id"] === NULL) {
            $httpReq->done(NULL);
            return;
        }
        $id = $res["_id"];
        $reply = new stdClass();
        $reply->from = $res["fromUserId"];
        $reply->time = $res["time"];
        $reply->body = $res["body"];
        $httpReq->done($reply);

        $storage->dropMessage($id);
    },
);
?>
