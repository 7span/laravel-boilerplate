🎯 Swagger Doc Rules (using OpenApi\Attributes as OA)

✅ Use: use OpenApi\Attributes as OA;
❌ Do NOT use @OA (no comment annotations)

1. Required Header:
- Add header param: X-Requested-With = XMLHttpRequest

2. POST/PUT APIs:
- Use fields from FormRequest
- Define OA\RequestBody with OA\JsonContent and OA\Property
- Include required fields

3. GET APIs:

1. From model's `$scopedFilters`:
- For each filter, generate a query param:
  name: `filter[{key}]`, in: `query`, type: `string`

2. From model's `$relationship`:
- Generate one query param:
  name: `include`, in: `query`
  description: `Comma-separated relations, e.g. relation1,relation2,...`
  type: `string`

3. From model's `$fillable`:
- Generate one query param:
  name: `sort`, in: `query`
  description: `Use field for asc, -field for desc`
  type: `string`

4. From resource's `whenLoadedMedia()` calls:
- Generate one query param:
  name: `media`, in: `query`
  description: `Comma-separated media keys from resource, e.g. key1,key2,...`
  type: `string`

4. Responses (simple only):
[
  new OA\Response(response: 200, description: 'Success'),
  new OA\Response(response: 400, description: 'Invalid input'),
  new OA\Response(response: 404, description: 'Not found')
]