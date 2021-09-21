# Log system
Adminx handles the log system. Adminx stores Create, Update and Delete actions logs for models.

Log fields:
- User: This log is about which user (who did this action): id of user
- Model: This log is about which model: slug of model
- Item: This log is about which item in the model: id of item
- Action: What user did (`create`, `update`, `delete`)
- Message: A message for log

You can store a custom log by yourself using `Adminx\Models\Log::add_log()` method:

```php
\Adminx\Models\Log::add_log('ModelSlug', $item_id, $user_id, $action, $message);
// for example
\Adminx\Models\Log::add_log('Post', 12, 5, 'create', 'something was created in table Post');
```

Also, By default Adminx has a page to show logs in `/admin/model/AdminxLog`.
Other models and items have a `History` button that is a link to this page.
Also this page has some options to filter the logs.

<img src="/doc/images/log-in-menu.png" />

<img src="/doc/images/log-user.png" />

<img src="/doc/images/log.png" />

---

[Previous: Models](04_models.md) | [Next: Plugin system](06_plugins.md)
