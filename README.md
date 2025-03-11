## Laravel Boilerplate

Welcome to the Laravel Boilerplate, crafted with Laravel (Version 12) as the backend framework. This boilerplate offers a robust foundation to jumpstart your new Laravel projects, complete with built-in basic authentication.

---

## Included Laravel Packages

This boilerplate comes equipped with the following Laravel packages:


- **[Laravel Telescope](https://laravel.com/docs/12.x/telescope)** - For monitoring and debugging requests.
- **[Laravel Littlegatekeeper](https://github.com/spatie/laravel-littlegatekeeper)** - Protects pages using a universal username/password combination. It secures the developer panel, including `Telescope`.
- **[Log Viewer](https://github.com/ARCANEDEV/LogViewer)** - Helps manage and track log files efficiently.
- **[Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)** - Provides a lightweight authentication system for SPAs, mobile applications, and token-based APIs.
- **[Laravel Pint](https://laravel.com/docs/12.x/pint)** - A code style fixer built on PHP-CS-Fixer to maintain clean and consistent code formatting.
  
  ### Code Formatting
  Before committing your changes, ensure your code adheres to the defined coding standards using Laravel Pint.

  Run Pint with the following command to check and fix only the modified files:
  
  ```sh
  ./vendor/bin/pint --dirty
  ```
  In addition, if you wish to use a pint.json from a specific directory, you may provide the --config option when invoking Pint:
  ```sh
  ./vendor/bin/pint --dirty --config=pint.json
  ```
- **[Larastan](https://github.com/larastan/larastan)** - Adds static typing to Laravel to enhance code quality and detect potential bugs.

  #### Important Note
  If you encounter issues committing code to Git, ensure the pre-commit hook has the correct permissions by running:
  ```sh
  chmod ug+x .hooks/pre-commit
  ```

---

## Supported APIs

The boilerplate includes the following API endpoints:

- **Register**
- **Login**
- **Get Profile**
- **Forgot and Reset Password**

---

## Developer Access Notes

- `Telescope`, `Log Viewer`, and other developer tools are protected by a universal username and password.
- These credentials are defined in the `.env` file under the variables:
  ```env
  DEVELOPER_USERNAME=
  DEVELOPER_PASSWORD=
  ```

Ensure that the credentials are set in your environment file before accessing developer tools.

---

This README provides a simple and structured guide to setting up and using the Laravel Boilerplate. Happy coding!
