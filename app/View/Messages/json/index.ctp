<?php
// don't send actions, but probably other fields not needed here too
foreach ($messages as &$message) {
    unset($message['Action']);
}
echo json_encode(compact('messages'));