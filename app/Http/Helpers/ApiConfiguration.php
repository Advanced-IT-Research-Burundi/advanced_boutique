<?php



function sendResponse($data, $message = 'Success', $code = 200, $error = []) {
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'error' => $error,
    ], $code);
}

function sendError($message, $code = 400, $error = []) {
    return response()->json([
        'success' => false,
        'message' => $message,
        'data' => null,
        'error' => $error,
    ], $code);
}
