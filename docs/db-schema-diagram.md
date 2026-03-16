# Database Schema Diagram

This diagram reflects the current schema defined in `database/schema.sql`.

```mermaid
erDiagram
    USERS {
        INT id PK
        VARCHAR name
        VARCHAR email UK
        VARCHAR password
        ENUM role
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    EMPLOYEES {
        INT id PK
        INT user_id FK
        VARCHAR designation
        VARCHAR phone
        VARCHAR department
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    PROJECTS {
        INT id PK
        VARCHAR name
        TEXT description
        DATE start_date
        DATE end_date
        ENUM status
        INT created_by FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    TASKS {
        INT id PK
        INT project_id FK
        INT assigned_to FK
        VARCHAR title
        TEXT description
        ENUM status
        DATE due_date
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    ACTIVITY_LOGS {
        INT id PK
        INT user_id FK
        TEXT action
        TIMESTAMP created_at
    }

    USERS ||--o| EMPLOYEES : "profile for"
    USERS ||--o{ PROJECTS : "creates"
    USERS ||--o{ TASKS : "is assigned"
    USERS ||--o{ ACTIVITY_LOGS : "performs"
    PROJECTS ||--o{ TASKS : "contains"
```

## Relationship Notes

- `employees.user_id -> users.id` with `ON DELETE CASCADE`
- `projects.created_by -> users.id` with `ON DELETE SET NULL`
- `tasks.project_id -> projects.id` with `ON DELETE CASCADE`
- `tasks.assigned_to -> users.id` with `ON DELETE SET NULL`
- `activity_logs.user_id -> users.id` with `ON DELETE SET NULL`

## Behavioral Notes From The Code

- Employee creation is a two-step transaction: insert into `users`, then insert into `employees`.
- Employee deletion removes both the `employees` row and its linked `users` row.
- Project and task writes are performed through service-layer functions in `src/services/`.
- State-changing API calls usually also append a row to `activity_logs`.
