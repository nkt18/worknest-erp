<?php

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/entities.php';
require_once __DIR__ . '/../helpers/errors.php';

function employeeListAll($conn)
{
    $result = $conn->query("
        SELECT employees.id, users.name, users.email, employees.designation, employees.phone, employees.department
        FROM employees
        JOIN users ON employees.user_id = users.id
        ORDER BY employees.id ASC
    ");

    return array_map('serializeEmployee', dbFetchAll($result));
}

function employeeCreate($conn, $data)
{
    $payload = validateEmployeeInput($data);
    $conn->begin_transaction();

    try {
        $password = password_hash('123456', PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users(name, email, password, role)
            VALUES(?, ?, ?, 'user')
        ");

        $stmt->bind_param('sss', $payload['name'], $payload['email'], $password);
        dbExecuteOrFail($stmt, 'Unable to create user account');
        $userId = $stmt->insert_id;

        $stmt = $conn->prepare("
            INSERT INTO employees(user_id, designation, phone, department)
            VALUES(?, ?, ?, ?)
        ");

        $stmt->bind_param('isss', $userId, $payload['designation'], $payload['phone'], $payload['department']);
        dbExecuteOrFail($stmt, 'Unable to create employee');

        $conn->commit();
    } catch (Throwable $exception) {
        $conn->rollback();
        throw $exception;
    }

    return $payload;
}

function employeeUpdate($conn, $id, $data)
{
    $payload = validateEmployeeInput($data);

    $stmt = $conn->prepare("
        UPDATE employees
        JOIN users ON employees.user_id = users.id
        SET users.name = ?, users.email = ?, employees.designation = ?, employees.phone = ?, employees.department = ?
        WHERE employees.id = ?
    ");

    $stmt->bind_param(
        'sssssi',
        $payload['name'],
        $payload['email'],
        $payload['designation'],
        $payload['phone'],
        $payload['department'],
        $id
    );

    dbExecuteOrFail($stmt, 'Unable to update employee');

    if ($stmt->affected_rows < 1) {
        $exists = $conn->prepare('SELECT id FROM employees WHERE id = ?');
        $exists->bind_param('i', $id);
        dbExecuteOrFail($exists, 'Unable to verify employee');

        if (!$exists->get_result()->fetch_assoc()) {
            throw new NotFoundException('Employee not found');
        }
    }

    return $payload;
}

function employeeDelete($conn, $id)
{
    $stmt = $conn->prepare('SELECT user_id FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    dbExecuteOrFail($stmt, 'Unable to load employee');

    $employee = $stmt->get_result()->fetch_assoc();

    if (!$employee) {
        throw new NotFoundException('Employee not found');
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare('DELETE FROM employees WHERE id = ?');
        $stmt->bind_param('i', $id);
        dbExecuteOrFail($stmt, 'Unable to delete employee');

        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $employee['user_id']);
        dbExecuteOrFail($stmt, 'Unable to delete employee user');

        $conn->commit();
    } catch (Throwable $exception) {
        $conn->rollback();
        throw $exception;
    }
}
