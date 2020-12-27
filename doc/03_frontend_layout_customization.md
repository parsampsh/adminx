# Frontend Layout Customization
You can customize adminx frontend **Base Layout view**. The default view as layout is `adminx.layouts.default`.

To customize it, you can use `set_layout` method:

```php
$admin->set_layout('my.view');
```

also you can get the seted view:

```php
$admin->get_view();
```

If should design your view(in above example, `my.view`) under the adminx standards.

### 1. Body content
Structure of your layout should be something like this:

```blade
<!--- Header --->

@yield('adminx_content')

<!--- Footer --->
```

The `adminx_layout` section contents page body content. you should yield this in your layout.

### 2. Page title
You should set page title of page like this example:

```blade
<!--- ... --->

<title>{{ $core->get_title() }} - @yield('adminx_title')</title>

<!--- ... --->
```

The `$core->get_title()` returns admin panel base title and `@yield('adminx_title')` returns current page title.

Also you can use another `$core` methods.

### 3. Urls
To generate urls to admin panel, you can use `$core->url('...')` method.

for example:

```blade
<a href="{{ $core->url('/') }}">Link to admin panel main page</a>
```

Actually, the `$core->url()` method returns something like this: `<admin-panel-base-route>/<the-route>`. for example `$core->url('/some/place')` returns `/admin/some/place`.

### 4. Menu
You can show admin panel menu under this structure:

```blade
@foreach($core->get_menu() as $item)
  @if($item['type'] === 'link')
    <li class="nav-item">
      <a class="nav-link" href="{{ $item['link'] }}" target="{{ $item['target'] }}">
      <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
    </li>
  @else
    @if($item['type'] === 'page')
      <li class="nav-item">
        <a class="nav-link" href="{{ $core->url('page/' . $item['slug']) }}" target="{{ $item['target'] }}">
        <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
      </li>
    @endif
  @endif
@endforeach
```

---

[Previous: Frontend language customization](02_lang.md) | [Next: Models](04_models.md)
