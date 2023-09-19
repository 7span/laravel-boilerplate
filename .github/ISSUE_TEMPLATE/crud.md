---
name: CRUD
about: Create, Read, Update and Delete with Ease
title: ''
labels: ''
assignees: ''

---

# Module <!-- Please replace this with the actual module name. -->
<!-- Provide a description of the module here.-->

## Modifications to Current Functionality
- [ ] We have a class called `App/Jobs/ExportData.php`, which will handle all the logic for exporting data based on different modules. To enable data export for a specific module, you should make adjustments within the 'ExportData' class by incorporating a switch case. Inside this switch case, please add your module and implement the export logic accordingly. In essence, you'll be required to call the `{ModuleExport.php}` from the newly defined switch case within `ExportData.php`.
- [ ] We have a class called `App/Jobs/ImportData.php`, which handles data import operations for different modules, including Excel validation. To enable data import for a specific module, you should make adjustments within the 'ProcessData' class by incorporating a switch case. Inside this switch case, please add your module and implement the import logic accordingly. In essence, you'll be required to call the `{ModuleImport.php}` from the newly defined switch case within `ImportData.php`.
- [ ] We have a service named `App/Services/ExportHistoryService.php` responsible for retrieving Export History based on the module. To obtain the Export History for {this} module, you should incorporate a switch case within the method of this service.
- [ ] We have a service named `App/Services/ImportExcelService.php` responsible for retrieving Import History based on the module. To obtain the Import History for {this} module, you should incorporate a switch case within the method of this service.
<!-- Describe all the necessary changes resulting from this modification. -->

## Naming Convention

| Entity | Filename | Location |
| --- | --- | --- | 
| Model | {Module.php}| App/Models |
| HTTP Request | {ModuleInsertRequest.php} | App/Http/Requests/{Module} | 
| HTTP Request | {ModuleUpdateRequest.php} | App/Http/Requests/{Module} | 
| HTTP Response | {ModuleCollection.php} | App/Http/Resources/{Module} | 
| HTTP Response | {ModuleResource.php} | App/Http/Resources/{Module} |
| Controller | {ModuleController.php} | App/Http/Controllers/Api/V1 | 
| Service | {ModuleService.php} | App/Services | 
| Observer | {ModuleObserver.php} | App/Observers |
| Imports | {ModuleImport.php} | App/Imports |
| Exports | {ModuleExport.php} | App/Exports |

## Database Design

#### 1. Table : <!-- Table Name -->

| Field | Datatype | Required |Note |
| --- | --- | --- | ------ |
| id | Int(8) | Yes | Primary Key |
| ... | ... | ... | ... |
| ... | ... | ... | ... |
| created_at | timestamp | Yes | Created timestamp |
| updated_at | timestamp | No |  |
| deleted_at | timestamp | No |  |
| created_by | Int(8) | Yes |  |
| updated_by | Int(8) | No |  |
| deleted_by | Int(8) | No |  |

## Relationship with Other Models
<!-- List of tables which have a relation with Module. -->

## Endpoints

**Base URL: `api/v1/`**

> [!IMPORTANT]
> **For all endpoints that require authentication, kindly ensure that the token is included in the Authorization header of the API request.**

| Endpoint         | Method | Input Argument            | Response             | Authentication Required | Description           |
|----------|--------|---------------|-------------------|-------------------|------------|
| /{module}        | Get    | [Listing Request](#listing) | [Listing Response](#listing-response) | Yes | The data will be returned with pagination by default. To retrieve all the data without pagination, simply provide **`per_page = -1`** as a parameter; this will bypass the pagination system. |
| /{module}        | Post   | [Insert Request](#insert)   | [Module](#module) | Yes | When inserting data, create an observer for the insertion process that automatically records the current timestamp as `created_at` and the ID of the authenticated user as `created_by`. |
| /{module}/{id}   | Get    |   | [Module](#module) | Yes | This is used to retrieve details about a specific object or item. |
| /{module}/{id}   | Put    | [Update Request](#update)   | [Module](#module) | Yes | When updating data, create an observer for the updation process that automatically records the current timestamp as `updated_at` and the ID of the authenticated user as `updated_by`|
| /{module}/{id}   | Delete |                         | [Success](#success) | Yes | When deleting data, create an observer for the deletion process that automatically records the current timestamp as `deleted_at` and the ID of the authenticated user as `deleted_by` |
| /{module}/export | Get | [Export Request](#export) | [Success](#success)  | Yes | The export process should be executed using queue jobs. **Users will receive the exported data in their email addresses.** |
| /{module}/import | Post | [Import Request](#import) | [Success](#success)  | Yes | The import process should be executed using queue jobs. |
|/{module}/export-history | Get | [Export History Request](#export-history)| [Export History](#export-history-response) | Yes | |
|/{module}/import-history | Get | [Import History Request](#import-history)| [Import History](#import-history-response) | Yes | |

## Request Object

1. <span id="listing">**Listing Request**</span>
```yaml
{
    page: Int               ## Page number for pagination
    per_page: Int           ## Number of items per page. Use -1 to retrieve all data, ignoring per_page and page.
    search: String          ## Search keyword or query
    sort: [
        by: String          ## Column name in the table (must match the database)
        order: String       ## Sorting order: "asc" (ascending) or "desc" (descending)
    ]
    filters: [
                            ## List of additional filters to be applied to the listing API
    ]
}
```

2. <span id="insert">**Insert Request**</span>
```yaml
{
     ## List of fields which need to be added.
}
```

> [!NOTE]
> **Create an HTTP Request class and name it {ModuleInsertRequest.php} to handle the validation of insert requests.**

| Input Field | Validation | 
| --- | --- |
| ... | ... |

3. <span id="update">**Update Request**</span>
```yaml
{
     ## List of fields which need to be updated.
}
```
> [!NOTE]
> **Create an HTTP Request class and name it {ModuleUpdateRequest.php} to handle the validation of update requests.**

| Input Field | Validation | 
| --- | --- |
| ... | ... |

4. <span id="export">**Export Request**</span>
```yaml
{
     search: String
     filters: [
           ## List of filters to be applied to the export API
     ]
}
```

5. <span id="import">**Import Request**</span>
```yaml
{
     action: String ## Possible Values: create, update
     media: File 
}
```

6. <span id="export-history">**Export History Request**</span>
```yaml
{
    page: Int               ## Page number for pagination
    per_page: Int           ## Number of items per page. Use -1 to retrieve all data, ignoring per_page and page.
    search: String          ## Search keyword or query
    sort: [
        by: String          ## Column name in the table (must match the database)
        order: String       ## Sorting order: "asc" (ascending) or "desc" (descending)
    ]
    filters: [
                            ## List of additional filters to be applied to the listing API
    ]
}
```

7. <span id="import-history">**Import History Request**</span>
```yaml
{
    page: Int               ## Page number for pagination
    per_page: Int           ## Number of items per page. Use -1 to retrieve all data, ignoring per_page and page.
    search: String          ## Search keyword or query
    sort: [
        by: String          ## Column name in the table (must match the database)
        order: String       ## Sorting order: "asc" (ascending) or "desc" (descending)
    ]
    filters: [
                            ## List of additional filters to be applied to the listing API
    ]
}
```

## Responses

1. **Pagination**
```yaml
 {
        from: Int
        to: Int
        total: Int
        per_page: Int
        current_page: Int
        next_page: Int
        previous_page: Int
        last_page: Int
}
```

2. <span id="module">**Module**</span>
```yaml
{
    id: Int
    ...
}
```

3. <span id="success">**Success response**</span>
```yaml
{
    status: Boolean
    message: String
}
```

> [!NOTE]
> **The table below outlines the messages defined for specific scenarios:**

| Scenario | Message | 
| --- | --- | 
| Import | Your file has been uploaded and is currently being processed. You can track its progress and view the status in the 'History' section. |
| Export | We are generating your file in the background. Once the file is ready, it will be sent to your registered email address. | 
| Delete | {Module} has been deleted successfully. | 

4. <span id="listing-response">**Listing Response**</span>
```yaml
{
    pagination: Pagination
    data: [Module]
}
```

5. <span id="export-history-response">**Export History Response**</span>
```yaml
{
    pagination: Pagination
    data: {
         id: Int
         status: String
         url: String
         media_name: String
         created_at: String
    }
}
```

6. <span id="import-history-response">**Import History Response**</span>
```yaml
{
    pagination: Pagination
    data: {
         id: Int
         status: String
         url: String
         media_name: String
         created_at: String
         action: String       ## Possible Values: create, update
    }
}
```
