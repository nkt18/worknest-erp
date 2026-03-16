# WorkNest ERP

## Tech Stack

- PHP
- MySQL
- Apache
- Bootstrap 5
- JavaScript Fetch API

## Features Implemented

- Secure login and logout
- Role-based access for `admin` and `user`
- Employee management
- Project management
- Task assignment and status updates
- Activity log tracking
- CSRF protection
- Session hardening
- Login rate limiting
- Clean URL routing with `.htaccess`

## Setup Instructions

1. Clone or download this project into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from XAMPP.
3. Create a database named `worknest_erp`.
4. Import [database/schema.sql](/Applications/XAMPP/xamppfiles/htdocs/worknest-erp/database/schema.sql).
5. Create a `.env` file in the project root with:

```ini
DB_HOST=localhost
DB_NAME=worknest_erp
DB_USER=root
DB_PASS=

BASE_URL=http://worknest.local
APP_NAME=WorkNest ERP
```

6. Configure your local virtual host or update `BASE_URL` to match your local project URL.
7. Open the app in the browser.

Live demo:
`https://worknest.page.gd/login`

## Test Credentials

Admin:
- Email: `admin@worknest.com`
- Password: `admin@073`

User:
- Email: `nitint8350@gmail.com`
- Password: `12345678`

Note:
New employees created from the admin panel currently get the default password `123456`.

## API Endpoints

- `GET /api/employees`
- `POST /api/employees`
- `PUT /api/employees?id=ID`
- `DELETE /api/employees?id=ID`
- `GET /api/projects`
- `POST /api/projects`
- `PUT /api/projects?id=ID`
- `DELETE /api/projects?id=ID`
- `GET /api/tasks`
- `POST /api/tasks`
- `PUT /api/tasks?id=ID`
- `DELETE /api/tasks?id=ID`
- `GET /api/users`
- `GET /api/activity-logs`

## Known Limitations

- No file upload module
- No email notifications
- No advanced reporting or analytics
- Free hosting environments may have performance and configuration limits

## Screenshots

Login Page:

![Login Page](image-2.png)

Admin Dashboard:

![Admin Dashboard](image-3.png)

Activity Logs:

![Activity Logs](image-8.png)
