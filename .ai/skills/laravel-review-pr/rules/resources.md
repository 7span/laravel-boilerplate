#### Lazy-Loading in Resources

Flag any relationship access inside a Resource `toArray()` that is not wrapped in `$this->whenLoaded()`. Unwrapped access (`$this->user->name`) causes a query per resource item.

#### Grouping Flat Keys with a Shared Prefix

Flag a Resource `toArray()`, or a service method that builds and returns an array directly (not via a Resource), that has 3 or more flat, separately-listed keys sharing the same prefix (e.g. `title_name`, `title_description`, `title_value`) instead of nesting them under a single key as a sub-array (`title` => `['name' => ..., 'description' => ..., 'value' => ...]`). Only flag when there are 3 or more such keys — do not flag 1 or 2 keys sharing a prefix, since nesting isn't worth it at that size.
