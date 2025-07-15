1. Generate the swagger document
----------------------------------------------------------------
Act like a senior backend API architect and expert in Swagger documentation and RESTful API design.

Objective:
Generate a complete L5-compliant Swagger (OpenAPI 3.0+) documentation specification for a RESTful API, based on the following rules:
- For all GET endpoints that return a collection, include the following default query parameters: 
  - `limit` (integer, with support for `-1` to return all items)
  - `page` (integer, for pagination)
  - `sort` (string, to define sorting order)
  - `include` (string, to define included relationships or fields)
- For POST and PUT endpoints, extract and define the request parameters from the referenced request schema files.
- Use accurate JSON Schema definitions where necessary.
- Assume standard REST naming conventions and resource structures.
- The Swagger output should include:
  - Tags for organizing endpoints
  - Descriptions and summaries for each path
  - Appropriate `200`, `201`, `400`, and `500` response objects with example payloads
  - Components including `schemas`, `parameters`, and `requestBodies`

Step-by-step:
1. Define the overall structure of the Swagger document (OpenAPI version, info block, servers).
2. For each endpoint, define the method (GET, POST, PUT, DELETE) and provide:
   - Tags, summary, and description
   - Path parameters, query parameters (if collection), and request body (if applicable)
   - Response status codes with JSON examples
3. For GET requests that return lists, include query parameters for `limit`, `page`, `sort`, and `include`.
4. For POST/PUT requests, define the `requestBody` using input from request files (assume JSON request examples or JSON Schema structure).
5. Define reusable components under `components.schemas`, `components.parameters`, and `components.requestBodies`.
6. Ensure consistent formatting and proper indentation for YAML/JSON validity.

Take a deep breath and work on this problem step-by-step.
========================================================