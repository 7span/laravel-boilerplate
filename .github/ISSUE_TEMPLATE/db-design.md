---
name: DB Design
about: Defining the structure of a database
title: ''
labels: ''
assignees: ''

---

# DB Design
We utilize [DBDiagram](https://dbdiagram.io/home) for generating our database schema. You can obtain the SQL file by exporting it from there.

### Here are the key points that require attention:

1. Export the SQL file and you can use it as a Database Schema, which can be added to the `database/schema folder`. This facilitates database setup by executing `php artisan migrate`. Additionally, you have to save it in the `Docs/Database` folder. 
2. Export the native format from the DB diagram and place it in the same `Docs/Database` folder.

### What if there are future changes?

1. Create a Migration file to address any modifications.
2. Update the schema using the dbdiagram tool, and once again, export both the SQL file and the native format. Save them in the "Docs/Database" folder.

### What is the reason for storing the files in the "Docs" folder?

We are storing this information for future reference. In the event that we need to make updates in the future and we lose access to the original DBDiagram account that generated the existing database schema, having this backup will allow us to effortlessly import it into another account and make the necessary updates to the database schema.
