# Log system
Adminx handles the log system. It stores Create, Update and Delete actions logs for models.

Log fields:
- User: Which user this log is about (who did this action): id of user
- Model: Which model this log is about: slug of model
- Item: Which item in model this log is about: id of item
- Action: The action (`create`, `update`, `delete`)
- Message: A message for log

You can store a custom log using `Adminx\Models\Log::addLog()` method:

```php
\Adminx\Models\Log::addLog('ModelSlug', $item_id, $user_id, $action, $message);
// for example
\Adminx\Models\Log::addLog('Post', 12, 5, 'create', 'something was created in table Post');
```

Also, By default Adminx has a page to show logs in `/admin/model/AdminxLog`.
Other models and items have a `History` button that is a link to this page.
Also this page has some options to filter the logs.

<img src="/doc/images/log-in-menu.png" />

<img src="/doc/images/log-user.png" />

<img src="/doc/images/log.png" />

---

[Previous: Models](04_models.md) | [Next: Plugin system](06_plugins.md)
