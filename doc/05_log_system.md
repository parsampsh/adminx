# Log system
Adminx handles log system. Adminx stores Create, Update and Delete actions log for models.

Log fields:
- User: This log is about which user (who does this action): id of user
- Model: This log is about which model: slug of model
- Item: This log is about which item in the model: id of item
- Action: what user does (`create`, `update`, `delete`)
- Message: a message for log

You can store a custom log by yourself using `Adminx\Models\Log::add_log()` method:

```php
\Adminx\Models\Log::add_log('ModelSlug', $item_id, $user_id, $action, $message);
// for example
\Adminx\Models\Log::add_log('Post', 12, 5, 'create', 'something was created in table Post');
```

Also, By default Adminx has a page to show logs in `/admin/model/AdminxLog`.
Other models and items have a `History` button that is a link to this page.
Also this page has some options to filter the logs.

TODO : add a images

---

[Previous: Models](04_models.md) | [Next: Plugin system](06_plugins.md)
