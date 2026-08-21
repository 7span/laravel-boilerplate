#### Dependency Injection Scope

**Constructor dependency not shared across methods.**
Flag when a class injects a dependency via `__construct()` (property or promoted) but fewer than two methods in the class actually use it — this includes a dependency used by zero methods (dead/unused) or by exactly one method. It is not mandatory for a dependency to be used by at least one method before being flagged; an unused constructor dependency is itself a finding. This forces every instantiation/resolution of the class to pay for a dependency most calls don't need, and bypasses the container if built with `new` directly (`new Address`) instead of being type-hinted in the constructor. Prefer resolving the dependency locally inside the single method that needs it (`app(Foo::class)` or `Foo::query()`), consistent with how other single-use dependencies are resolved elsewhere in the same class.

**Shared dependency resolved manually instead of constructor-injected.**
The opposite mistake: a dependency used by two or more methods is resolved with `app(Foo::class)` inside the constructor body (or anywhere else) instead of being type-hinted as a promoted constructor parameter. This bypasses the container's normal injection, hides the dependency from anyone reading the constructor signature, and makes the class harder to unit test (no way to pass a mock without overriding the container binding). Flag this even when the dependency is genuinely shared — the fix is always to promote it to a typed constructor parameter, not to leave the `app()` call in place.

Flag when:
- A constructor property/promoted dependency is referenced in zero or exactly one method
- A dependency is built with `new ClassName` in the constructor instead of being container-resolved
- A dependency used by two or more methods is resolved via `app(Foo::class)` (or similar) instead of a type-hinted constructor parameter
