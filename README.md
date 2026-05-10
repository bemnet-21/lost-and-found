# Lost and Found

A full-stack web application for managing lost and found items, designed for schools or organizations. Users can report lost or found items, manage their listings, and resolve or delete items. Built with React (Vite) frontend and PHP (REST API) backend, with a PostgreSQL database (see schema).

## Features

- User registration, login, and session management
- Report lost or found items with images and details
- View, search, and filter items
- Mark items as resolved or delete them
- Secure authentication and cross-site session support
- Responsive, modern UI

## Tech Stack

- **Frontend:** React (Vite), Context API, Fetch API
- **Backend:** PHP (RESTful API), PDO, MVC structure
- **Database:** PostgreSQL (schema in `database.pgsql.sql`)
- **Deployment:** Frontend (Vercel), Backend (Railway)

## Project Structure

```
lost-and-found/
│
├── client/                # React frontend (Vite)
│   ├── src/
│   │   ├── components/    # Reusable UI components
│   │   ├── context/       # Auth context
│   │   ├── lib/           # API utilities
│   │   └── pages/         # Main app pages
│   └── ...                # Vite config, package.json, etc.
│
├── server/                # PHP backend (REST API)
│   ├── api/               # API endpoints (login, register, items, etc.)
│   ├── config/            # DB and session config
│   ├── controllers/       # Auth and item controllers
│   ├── models/            # User and item models
│   ├── utils/             # CORS, response, validation helpers
│   └── uploads/           # Uploaded item images
│
├── database.pgsql.sql           # schema
└── README.md
```

## Database Schema

- **users:** id, username, email, password, created_at
- **items:** id, user_id, title, description, category, type (lost/found), location, image_path, status (active/resolved), created_at
- **messages:** id, item_id, sender_id, receiver_id, content, sent_at

See `database.pgsql.sql` for the full PostgreSQL schema and constraints.

## Setup & Deployment

### Prerequisites

- Node.js (for frontend)
- PHP 8+ (for backend)
- PostgreSQL or compatible DB

### Local Development

1. **Clone the repo:**
	```
	git clone https://github.com/yourusername/lost-and-found.git
	```

2. **Database:**
	- Create a database and import `database.sql`.
	- Set `DATABASE_URL` in backend environment.

3. **Backend:**
	- Configure environment variables (DB connection, etc.).
	- Serve `server/` with PHP (e.g., built-in server or Railway).

4. **Frontend:**
	```
	cd client
	npm install
	npm run dev
	```

5. **CORS & Session:**
	- CORS and session cookies are configured for cross-site deployment (Vercel + Railway).
	- For local dev, use `localhost:3000` (frontend) and your backend URL.

### Production

- Deploy frontend to Vercel.
- Deploy backend to Railway.
- Update allowed origins and session cookie domain in backend config if your domain changes.

## API Endpoints

- `POST /api/login.php` — User login
- `POST /api/register.php` — User registration
- `POST /api/logout.php` — Logout
- `GET /api/check-auth.php` — Check session/auth
- `GET /api/items/read.php` — List/search items
- `POST /api/items/create.php` — Create item
- `PUT /api/items/update.php` — Update/resolve item
- `DELETE /api/items/delete.php` — Delete item

## Security

- Passwords hashed with bcrypt
- Session cookies: HttpOnly, Secure, SameSite=None (for cross-site)
- CORS restricted to trusted origins

## License

MIT