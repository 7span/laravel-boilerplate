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

2. Additionally, save this SQL file in the `Docs/Database` folder with the name `database.sql`.

### Handling Future Changes:

If there are any future changes to the database structure, it is essential to ensure that both your codebase and the DBDiagram schema are updated. Follow the steps below to manage these changes effectively:

1. **Codebase Modifications:** Create a new migration file to address codebase modifications, considering the existing schema that has already been migrated.

2. **Update DBDiagram Schema:** To reflect the changes accurately, utilize the DBDiagram tool. Follow these steps:
     - Make the necessary adjustments to the `DBDiagram` schema to accommodate the changes.
     - Export both the SQL file and the native format from `DBDiagram`.
     - Save these updated files in the "**Docs/Database**" folder.

By following these steps, you ensure that your codebase and database schema remain synchronized, making it easier to manage future changes effectively.

### Why Store Files in the "Docs" Folder?

We store this information for future reference. In case we need to make updates in the future and lose access to the original DBDiagram account that generated the existing database schema, having this backup will allow us to easily import it into another account and make the necessary updates to the database schema.
