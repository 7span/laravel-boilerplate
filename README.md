## About Laravel Boilerplate

The laravel boilerplate is project that uses the `Laravel` (Version 11) as a backend framework. It provides basic structure for starting any new laravel project. It has implemented the basic authetication provided by laravel.

## Laravel Packages

-   [Laravel Telescope](https://laravel.com/docs/11.x/telescope) - For Monitoring request
-   [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - Horizon allows you to easily monitor key metrics of your queue system such as job throughput, runtime, and job failures.
-   [Laravel Littlegatekeeper](https://github.com/spatie/laravel-littlegatekeeper) - Protect pages from access with a universal username/password combination. It is used for developer panel which includes `Telescope`.
-   [Log Viewer](https://github.com/ARCANEDEV/LogViewer) - To manage and keep track of each one of your log files
-   [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) - It provides a featherweight authentication system for SPAs , mobile applications, and simple, token based APIs
-   [Laravel Pint](https://laravel.com/docs/11.x/pint) - It provides an opinionated PHP code style fixer for minimalists. Pint is built on top of PHP-CS-Fixer and makes it simple to ensure that your code style stays clean and consistent.

        -   **General Usage**
            to use the pint binary all you have to do is run the following command in the root of your project.

            ```
            ./vendor/bin/pint
            ```

        -   **VS Code - Add Keyboard Shortcut to run Laravel Pint** 1. Open VS Code. 2. Open Keyboard shortcut panel, either from `file -> preferences -> keyboard Shortcuts` or `Ctrl+k Ctrl+s` 3. Ones open click on the file icon on the top to open the json file of the keyboard shortcuts. 4. Ones open add the following lines to the file.
            `       [

    {
    "key": "ctrl+s",
    "command": "workbench.action.tasks.runTask",
    "args": "Pint Formatter"
    }
    ]`
    Now when you hit Ctrl + s and your laravel project will be formatted with laravel pint.

-   [Larastan](https://github.com/larastan/larastan) - Adds static typing to Laravel to improve developer productivity and code quality , Discovers bugs in your code.

    -   **Note:**
        In any case, if you are unable to commit the code to Git, then run the command below.

        ```
        chmod ug+x .hooks/pre-commit
        ```

-   [Swagger API Documentation](https://swagger.io/docs/specification/about/) - Swagger is a set of open-source tools built around the OpenAPI Specification that can help you design, build, document and consume REST APIs.

## Supported APIs

-   Register
-   Login
-   Get Profile
-   Forget and Reset Password
-   Change Password

## Notes

1. `Telescope` , `Log Viewer` and other developer packages are accessible with universal username and password defined in .env file under `DEVELOPER_USERNAME` and `DEVELOPER_PASSWORD`.
