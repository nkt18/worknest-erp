<?php

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/entities.php';
require_once __DIR__ . '/../helpers/errors.php';

function taskListForAdmin($conn)
{
    $result = $conn->query("
        SELECT tasks.*, projects.name AS project_name, users.name AS user_name
        FROM tasks
        LEFT JOIN projects ON tasks.project_id = projects.id
        LEFT JOIN users ON tasks.assigned_to = users.id
        ORDER BY tasks.id DESC
    ");

    return array_map('serializeTask', dbFetchAll($result));
}

function taskListForUser($conn, $userId)
{
    $stmt = $conn->prepare("
        SELECT tasks.*, projects.name AS project_name, users.name AS user_name
        FROM tasks
        LEFT JOIN projects ON tasks.project_id = projects.id
        LEFT JOIN users ON tasks.assigned_to = users.id
        WHERE tasks.assigned_to = ?
        ORDER BY tasks.id DESC
    ");

    $stmt->bind_param('i', $userId);
    dbExecuteOrFail($stmt, 'Unable to load tasks');

    return array_map('serializeTask', dbFetchAll($stmt->get_result()));
}

function taskCreate($conn, $data)
{
    $payload = validateTaskInput($data);

    $stmt = $conn->prepare("
        INSERT INTO tasks (title, description, project_id, assigned_to, status, due_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        'ssiiss',
        $payload['title'],
        $payload['description'],
        $payload['project_id'],
        $payload['assigned_to'],
        $payload['status'],
        $payload['due_date']
    );

    dbExecuteOrFail($stmt, 'Unable to create task');

    return $payload;
}

function taskUpdateByAdmin($conn, $id, $data)
{
    $payload = validateTaskInput($data);

    $stmt = $conn->prepare("
        UPDATE tasks
        SET title = ?, description = ?, project_id = ?, assigned_to = ?, status = ?, due_date = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        'ssiissi',
        $payload['title'],
        $payload['description'],
        $payload['project_id'],
        $payload['assigned_to'],
        $payload['status'],
        $payload['due_date'],
        $id
    );

    dbExecuteOrFail($stmt, 'Unable to update task');

    if ($stmt->affected_rows < 1) {
        $exists = $conn->prepare('SELECT id FROM tasks WHERE id = ?');
        $exists->bind_param('i', $id);
        dbExecuteOrFail($exists, 'Unable to verify task');

        if (!$exists->get_result()->fetch_assoc()) {
            throw new NotFoundException('Task not found');
        }
    }

    return $payload;
}

function taskUpdateStatusForUser($conn, $id, $userId, $status)
{
    $normalizedStatus = validateTaskStatusInput($status);

    $stmt = $conn->prepare("
        UPDATE tasks
        SET status = ?
        WHERE id = ? AND assigned_to = ?
    ");

    $stmt->bind_param('sii', $normalizedStatus, $id, $userId);
    dbExecuteOrFail($stmt, 'Unable to update task status');

    if ($stmt->affected_rows < 1) {
        $exists = $conn->prepare('SELECT id FROM tasks WHERE id = ? AND assigned_to = ?');
        $exists->bind_param('ii', $id, $userId);
        dbExecuteOrFail($exists, 'Unable to verify task');

        if (!$exists->get_result()->fetch_assoc()) {
            throw new NotFoundException('Task not found');
        }
    }
}

function taskDelete($conn, $id)
{
    $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->bind_param('i', $id);
    dbExecuteOrFail($stmt, 'Unable to delete task');

    if ($stmt->affected_rows < 1) {
        throw new NotFoundException('Task not found');
    }
}
