---
name: new-api-endpoint
description: Add or change a JSON endpoint in routes/api.php of the report card app. Use whenever the user asks for a new API endpoint, route or JSON response for the phone app.
---

# Add a JSON API endpoint

Follow these steps in order. Show me the plan (the files you will touch) before you write anything.

1. **Design first.** Write the verb, URL, who may call it, the JSON it returns and every status code it can give (200, 201, 204, 401, 403, 404, 422). Plural nouns, no verbs in the URL: `/api/sections/{section}/students`, not `/api/getStudents`.
2. **Route.** Add it in `routes/api.php`, inside the `auth:sanctum` group. Use route model binding (`{section}`), so a missing id gives 404 for free.
3. **Controller.** Put the method in a controller in `app/Http/Controllers/Api`. First line: `Gate::authorize(...)` with the right Policy. If no Policy covers it yet, stop and ask me.
4. **No new rules in the controller.** Averages, remarks and anything else about grades come from `ReportCardService`. If the service doesn't have what you need, add a method there.
5. **Shape the JSON.** Return `['data' => ...]` with an explicit list of fields, or an API Resource. Never return a whole model: it leaks columns like timestamps and foreign keys.
6. **Input.** If the endpoint takes input, validate it with a Form Request and save only `$request->validated()`.
7. **Tests.** Add Pest tests to `tests/Feature/ApiTest.php`, using `lizaToken()`: one for the happy path, one for 403 (another teacher's data), and one for 422 if there is input. Run `php artisan test` and show me the result.
8. **Postman.** Finish by telling me the exact request to add to the Report Cards API collection: method, URL with `{{base_url}}`, body, and a test script that checks the status code and one field.