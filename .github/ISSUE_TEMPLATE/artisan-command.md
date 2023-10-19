---
name: Artisan Command
about: Build your own custom commands.
title: Generate a custom artisan command
labels: ''
assignees: ''

---

# Artisan Command

Create an artisan command for...
<!-- Provide a description for the feature you intend to create an Artisan command for. -->

## Steps to Create an Artisan Command

1. Generate an Artisan command using `php artisan make:command {COMMANDNAME}`, which will generate a file inside the `app/Console` folder.
2. Add a signature to the command, like `{project-short-form}:{feature}`, e.g., `vepaar:send-reminder`.
3. Utilize the Queue for execution instead of directly coding within the handle method.
4. Define the list of supported arguments. 

## Command Illustration
`php artisan {project-short-form}:{feature} --{supported-arguments}`

## Argument Information
<!-- Delete this block if your command doesn't require any arguments. -->

| Argument | Default Value | Description |
| --- | --- | --- | 
| <!-- Argument List --> | <!-- In case you have any default value set for the argument  --> | Appropriate description for the provided argument. |
