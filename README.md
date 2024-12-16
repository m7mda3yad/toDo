# ToDo Application


## Prerequisites

Before running the application, make sure you have the following software installed:

- **PHP 8.1+** or higher
- **Composer** (for PHP dependency management)
- **Node.js** and **npm** (for frontend dependency management)
- **MySQL** or any other database supported by Laravel
- **Laravel 8+** or the version used in the project
- **Vue.js** (Frontend)

4. **Set Up Authentication**:

## Steps to Install and Run the Application

### 1. Clone the Repository

Clone the repository to your local machine.

```bash
git clone https://github.com/m7mda3yad/toDo.git
```
## 2. Install Project

```bash
composer install
php artisan migrate
php artisan db:seed
npm install
npm run dev
php artisan serve


```
### Environment Requirements

2. **Create a Database**: Create a MySQL database (or any other supported DB) for the app, and update the `.env` file with your database credentials.

3. **Generate Application Key**: Run the following command to generate a unique key for the application.

    ```bash
    php artisan key:generate
    ```
