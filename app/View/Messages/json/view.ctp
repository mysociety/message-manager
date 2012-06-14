<?php
// don't send actions, but probably other fields not needed here too
unset($message['Action']);

echo json_encode(compact('message'));