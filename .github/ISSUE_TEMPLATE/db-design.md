---
name: DB Design
about: Defining the structure of a database
title: ''
labels: ''
assignees: ''

---

# DB Design
We use [DBDiagram](https://dbdiagram.io/home) to generate our database schema. You can obtain the SQL file by exporting it from there.

### Key Points to Consider:

1. Export the SQL file from DBDiagram, and place it in the `database/schema` folder. This makes it easy to set up the database by running `php artisan migrate`.

   ![Database Schema](https://github.com/7span/patidar-melap/assets/19200178/fd6dab12-cfe9-486c-b635-1790c378f192)

2. Additionally, save this SQL file in the `docs/database` folder with the name `database.sql`.

3. In parallel, store the native format in the `docs/database` folder, naming it `database.dbml`. The native format refers to the code located on the left side used to generate the schema.
<img width="987" alt="image" src="https://github.com/7span/laravel-boilerplate/assets/19200178/bafb9978-b727-43dd-8a42-86ea5b8f16c0">


### Handling Future Changes:

If there are any future changes to the database structure, it is essential to ensure that both your codebase and the DBDiagram schema are updated. We have two effective methods for managing these changes:

#### 1. Update the DBDiagram Schema Manually:

-  **Codebase Modifications:** Create a new migration file to address codebase modifications, considering the existing schema that has already been migrated.

- **Update DBDiagram Schema:** To reflect the changes accurately, utilize the DBDiagram tool. Follow these steps:

     - Make the necessary adjustments to the `DBDiagram` schema to accommodate the changes.
     - Export both the SQL file and the native format from `DBDiagram`.
     - Save these updated files in the "**docs/database**" folder.
     
> [!IMPORTANT]
> **This is our preferred approach.**

#### 2. Update the DBDiagram Schema with Import SQL:

-  **Codebase Modifications:** Create a new migration file to address codebase modifications, considering the existing schema that has already been migrated. Next, execute the `php artisan schema:dump` command, which will remove the migration files and generate a new SQL file.

- **Update DBDiagram Schema:** To accurately represent the changes, leverage the DBDiagram tool's import functionality by importing the SQL file. This action will regenerate the DBDiagram Schema.

> [!NOTE]
> **While we generally prefer the first approach, if you choose the second method, ensure that any new changes are also added to the DBDiagram schema.**

By following these procedures, you maintain synchronization between your codebase and database schema, allowing for effective management of future changes.

**After these changes please ensure that you update both files, namely `database.sql` and `database.dbml`, located in the `docs/database` folder.**

### Why Store Files in the "Docs" Folder?

We store this information for future reference. In case we need to make updates in the future and lose access to the original DBDiagram account that generated the existing database schema, having this backup will allow us to easily import it into another account and make the necessary updates to the database schema.
