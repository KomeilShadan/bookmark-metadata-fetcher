
---

# **Laravel Bookmark Metadata Fetcher**

A simple Laravel application that allows users to store bookmarks, fetch metadata using a background job, and retrieve stored bookmarks. This project uses a Docker setup for easy environment configuration and includes `make` commands for streamlined development without needing to manually interact with containers.

---

## **Setup Instructions**

### **1. Clone the Repository**
```bash
git clone https://github.com/KomeilShadan/bookmark-metadata-fetcher.git
cd bookmark-metadata-fetcher
```

### **2. Build and Start the Project**
Run the following command to build and start the Docker containers:
```bash
make up
```

### **3. Install Dependencies**
- Install PHP dependencies:
  ```bash
  make composer install
  ```
- Set up the `.env` file:
  ```bash
  cp .env.example .env
  ```
- Generate the application key:
  ```bash
  make art key:generate
  ```

### **4. Run Migrations**
Run the database migrations to create the necessary tables:
```bash
make art migrate
```

### **5. Access the Application**
The application will be available at:
```
http://localhost:8080
```

---

## **Endpoints**

### **1. Store a Bookmark**
- **Endpoint:** `POST /api/bookmarks`
- **Request Body:**
  ```json
  {
    "url": "https://google.com"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Bookmark submitted successfully!",
    "data": {
      "id": "some-uuid",
      "url": "https://google.com",
      "title": "Example Title",
      "description": "Example Description"
    }
  }
  ```

### **2. Retrieve Bookmarks**
- **Endpoint:** `GET /api/bookmarks`
- **Response:**
  ```json
  {
    "data": [
      {
        "id": "some-uuid",
        "url": "https://google.com",
        "title": "Example Title",
        "description": "Example Description",
        "created_at": "2025-03-07T10:00:00Z"
      }
    ]
  }
  ```

---

## **Background Job**

- When a bookmark is stored, a background job is triggered to fetch metadata (e.g., title and description) for the provided URL.
- The metadata is then stored in the database and associated with the bookmark.

---

## **Testing**

Run the following command to execute tests:
```bash
make test
```

---

## **Makefile Commands**

The project is set up with `make` commands to simplify common tasks. Here are some useful commands:

### **Artisan Commands**
You can run Laravel Artisan commands without entering the container:
```bash
make art <command>
```
Example:
```bash
make art route:list
```

### **Composer Commands**
Run Composer commands with:
```bash
make composer <command>
```

### **Code Formatting**
- Format changed files:
  ```bash
  make pint format
  ```
- Format all files:
  ```bash
  make pint format-all
  ```

### **Other Commands**
| Command            | Description                                  |
|--------------------|----------------------------------------------|
| `make build`       | Run `docker compose build`.                 |
| `make ps`          | List running containers.                    |
| `make up`          | Start the Docker containers.                |
| `make down`        | Stop the Docker containers.                 |
| `make down-volumes`| Stop containers and remove volumes.         |
| `make restart`     | Restart the Docker containers.              |
| `make composer-install` | Run `composer install`.                |
| `make tinker`      | Run `artisan tinker`.                       |
| `make migration`   | Create a new database migration.            |
| `make migrate`     | Run database migrations.                    |
| `make horizon`     | Run Laravel Horizon.                        |
| `make install-laravel` | Download Laravel source and set up `.env`. |
| `make test`        | Run tests.                                  |

---

## **Contact**

For any questions or issues, feel free to contact **Komeil Shadannejad** at **kshadannejad@gmail.com**.

---
