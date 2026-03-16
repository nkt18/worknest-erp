# Request Flow Diagram

These diagrams reflect the current PHP request flow used by the app.

## High-Level Application Flow

```mermaid
flowchart TD
    A[Browser] --> B[index.php or direct route]
    B --> C{Route type}

    C -->|Login page| D[src/auth/login.php]
    C -->|Admin page| E[src/admin/...]
    C -->|User page| F[src/user/...]
    C -->|API endpoint| G[src/api/...]

    E --> H[src/middleware/admin.php]
    F --> I[src/middleware/user.php]
    G --> J[src/middleware/auth.php]

    H --> J
    I --> J

    J --> K[enforceSessionSecurity]
    K --> L{Authenticated?}
    L -->|No| M[Redirect to /login or 401 JSON]
    L -->|Yes| N[requireCsrfToken for state-changing requests]

    N --> O{Role check needed?}
    O -->|Admin only| P[requireRole admin]
    O -->|User only| Q[requireRole user]
    O -->|No extra role gate| R[Continue]

    P --> R
    Q --> R

    R --> S[Controller/page or API file]
    S --> T[Helper parsing and validation]
    T --> U[Service layer in src/services]
    U --> V[dbConnection and prepared SQL]
    V --> W[(MySQL tables)]
    U --> X[logActivity on writes]
    X --> W
    W --> Y[HTML page or JSON response]
    Y --> A
```

## Login Request Flow

```mermaid
flowchart TD
    A[Browser GET /login] --> B[src/auth/login.php]
    B --> C[startSecureSession]
    C --> D{Already logged in?}
    D -->|Yes admin| E[Redirect /admin/dashboard]
    D -->|Yes user| F[Redirect /user/dashboard]
    D -->|No| G[Render login form with CSRF token]

    H[Browser POST /login] --> B
    B --> I[verifyCsrfToken]
    I --> J{Token valid?}
    J -->|No| K[Show invalid request token error]
    J -->|Yes| L[Validate email and password presence]
    L --> M[loginRateLimitGuard by email]
    M --> N{Allowed to continue?}
    N -->|No| O[Show retry-after error]
    N -->|Yes| P[Query users by email]
    P --> Q{User found?}
    Q -->|No| R[loginRateLimitFailure]
    R --> S[Show invalid credentials]
    Q -->|Yes| T[password_verify]
    T --> U{Password matches?}
    U -->|No| R
    U -->|Yes| V[loginRateLimitClear]
    V --> W[loginUserSession]
    W --> X[Store user_id user_name user_role and session security fields]
    X --> Y{Role}
    Y -->|admin| E
    Y -->|user| F
```

## API Write Flow

```mermaid
flowchart TD
    A[Frontend fetch PUT POST DELETE /api/... ] --> B[src/api/*.php]
    B --> C[src/middleware/auth.php]
    C --> D[enforceSessionSecurity]
    D --> E[requireCsrfToken]
    E --> F{Role allowed?}
    F -->|No| G[403 JSON]
    F -->|Yes| H[apiReadJson and apiRequireId]
    H --> I[Service function]
    I --> J[validate*Input helpers]
    J --> K[Prepared SQL via mysqli]
    K --> L[(users projects tasks employees activity_logs)]
    I --> M[logActivity for create update delete]
    M --> L
    L --> N[apiResponse success JSON]
```

## Concrete Paths Covered

- Login and session bootstrap: `src/auth/login.php`, `src/helpers/session.php`
- Shared auth gate: `src/middleware/auth.php`
- Role wrappers: `src/middleware/admin.php`, `src/middleware/user.php`
- APIs: `src/api/projects.php`, `src/api/tasks.php`, `src/api/employees.php`, `src/api/activity_logs.php`, `src/api/users.php`
- Services: `src/services/ProjectService.php`, `src/services/TaskService.php`, `src/services/EmployeeService.php`, `src/services/ActivityLogService.php`
- Database access: `src/helpers/database.php`, `src/config/database.php`
