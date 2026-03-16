<?php

require_once __DIR__ . '/errors.php';

function validateEnum($value, $allowedValues, $message)
{
    if (!in_array($value, $allowedValues, true)) {
        throw new ValidationException($message);
    }

    return $value;
}

function validateDateOrNull($value, $message)
{
    if ($value === null || $value === '') {
        return null;
    }

    $normalized = (string) $value;
    $date = DateTime::createFromFormat('Y-m-d', $normalized);

    if (!$date || $date->format('Y-m-d') !== $normalized) {
        throw new ValidationException($message);
    }

    return $normalized;
}

function validateProjectInput($data)
{
    $name = trim((string) ($data['name'] ?? ''));

    if ($name === '') {
        throw new ValidationException('Project name is required');
    }

    return [
        'name' => $name,
        'description' => trim((string) ($data['description'] ?? '')),
        'status' => validateEnum((string) ($data['status'] ?? 'Active'), ['Active', 'On Hold', 'Completed'], 'Invalid project status'),
        'start_date' => validateDateOrNull($data['start_date'] ?? null, 'Invalid project start date'),
        'end_date' => validateDateOrNull($data['end_date'] ?? null, 'Invalid project end date'),
    ];
}

function serializeProject($row)
{
    return [
        'id' => (int) ($row['id'] ?? 0),
        'name' => (string) ($row['name'] ?? ''),
        'description' => $row['description'] ?? '',
        'status' => (string) ($row['status'] ?? 'Active'),
        'start_date' => $row['start_date'] ?? null,
        'end_date' => $row['end_date'] ?? null,
        'created_by' => isset($row['created_by']) ? (int) $row['created_by'] : null,
        'created_at' => $row['created_at'] ?? null,
        'updated_at' => $row['updated_at'] ?? null,
    ];
}

function validateTaskInput($data)
{
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $projectId = (int) ($data['project_id'] ?? 0);
    $assignedTo = (int) ($data['assigned_to'] ?? 0);

    if ($title === '') {
        throw new ValidationException('Title required');
    }

    if ($description === '') {
        throw new ValidationException('Description required');
    }

    if ($projectId <= 0 || $assignedTo <= 0) {
        throw new ValidationException('Project and assignee are required');
    }

    return [
        'title' => $title,
        'description' => $description,
        'project_id' => $projectId,
        'assigned_to' => $assignedTo,
        'status' => validateEnum((string) ($data['status'] ?? 'pending'), ['pending', 'in_progress', 'completed'], 'Invalid task status'),
        'due_date' => validateDateOrNull($data['due_date'] ?? null, 'Invalid due date'),
    ];
}

function validateTaskStatusInput($status)
{
    return validateEnum((string) ($status ?? 'pending'), ['pending', 'in_progress', 'completed'], 'Invalid task status');
}

function serializeTask($row)
{
    return [
        'id' => (int) ($row['id'] ?? 0),
        'project_id' => isset($row['project_id']) ? (int) $row['project_id'] : null,
        'assigned_to' => isset($row['assigned_to']) ? (int) $row['assigned_to'] : null,
        'title' => (string) ($row['title'] ?? ''),
        'description' => $row['description'] ?? '',
        'status' => (string) ($row['status'] ?? 'pending'),
        'due_date' => $row['due_date'] ?? null,
        'project_name' => $row['project_name'] ?? null,
        'user_name' => $row['user_name'] ?? null,
        'created_at' => $row['created_at'] ?? null,
        'updated_at' => $row['updated_at'] ?? null,
    ];
}

function validateEmployeeInput($data)
{
    $payload = [
        'name' => trim((string) ($data['name'] ?? '')),
        'email' => trim((string) ($data['email'] ?? '')),
        'designation' => trim((string) ($data['designation'] ?? '')),
        'phone' => trim((string) ($data['phone'] ?? '')),
        'department' => trim((string) ($data['department'] ?? '')),
    ];

    foreach ($payload as $value) {
        if ($value === '') {
            throw new ValidationException('All fields required');
        }
    }

    if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
        throw new ValidationException('A valid email is required');
    }

    return $payload;
}

function serializeEmployee($row)
{
    return [
        'id' => (int) ($row['id'] ?? 0),
        'name' => (string) ($row['name'] ?? ''),
        'email' => (string) ($row['email'] ?? ''),
        'designation' => (string) ($row['designation'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'department' => (string) ($row['department'] ?? ''),
    ];
}

function serializeUserSummary($row)
{
    return [
        'id' => (int) ($row['id'] ?? 0),
        'name' => (string) ($row['name'] ?? ''),
    ];
}

function validateActivityLogFilters($filters)
{
    $page = max(1, (int) ($filters['page'] ?? 1));
    $limit = max(1, min(50, (int) ($filters['limit'] ?? 5)));

    return [
        'search' => trim((string) ($filters['search'] ?? '')),
        'user_id' => max(0, (int) ($filters['user_id'] ?? 0)),
        'page' => $page,
        'limit' => $limit,
    ];
}

function serializeActivityLog($row)
{
    return [
        'id' => (int) ($row['id'] ?? 0),
        'user_id' => isset($row['user_id']) ? (int) $row['user_id'] : null,
        'user_name' => $row['user_name'] ?? null,
        'action' => (string) ($row['action'] ?? ''),
        'created_at' => $row['created_at'] ?? null,
    ];
}
