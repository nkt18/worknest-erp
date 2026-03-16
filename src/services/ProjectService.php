<?php

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/entities.php';
require_once __DIR__ . '/../helpers/errors.php';

function projectListAll($conn)
{
    $result = $conn->query("
        SELECT *
        FROM projects
        ORDER BY id ASC
    ");

    return array_map('serializeProject', dbFetchAll($result));
}

function projectCreate($conn, $data, $actorId)
{
    $payload = validateProjectInput($data);

    $stmt = $conn->prepare("
        INSERT INTO projects (name, description, status, start_date, end_date, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        'sssssi',
        $payload['name'],
        $payload['description'],
        $payload['status'],
        $payload['start_date'],
        $payload['end_date'],
        $actorId
    );

    dbExecuteOrFail($stmt, 'Unable to create project');

    return $stmt->insert_id;
}

function projectUpdate($conn, $id, $data)
{
    $payload = validateProjectInput($data);

    $stmt = $conn->prepare("
        UPDATE projects
        SET name = ?, description = ?, status = ?, start_date = ?, end_date = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        'sssssi',
        $payload['name'],
        $payload['description'],
        $payload['status'],
        $payload['start_date'],
        $payload['end_date'],
        $id
    );

    dbExecuteOrFail($stmt, 'Unable to update project');

    if ($stmt->affected_rows < 1) {
        $exists = $conn->prepare('SELECT id FROM projects WHERE id = ?');
        $exists->bind_param('i', $id);
        dbExecuteOrFail($exists, 'Unable to verify project');

        if (!$exists->get_result()->fetch_assoc()) {
            throw new NotFoundException('Project not found');
        }
    }
}

function projectDelete($conn, $id)
{
    $stmt = $conn->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->bind_param('i', $id);
    dbExecuteOrFail($stmt, 'Unable to delete project');

    if ($stmt->affected_rows < 1) {
        throw new NotFoundException('Project not found');
    }
}
