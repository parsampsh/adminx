# Frontend Layout Customization
You can customize Adminx frontend **Base Layout view**. The default view as layout is `adminx.layouts.default`.

To customize it, you can use `setLayout` method:

```php
$admin->setLayout('my.view');
```

also you can get the seted view:

```php
$admin->getLayout();
```

You should design your view(in above example, `my.view`) under the Adminx standards.

## 1. Body content
Structure of your layout should be something like this:

```blade
<!--- Header --->

@yield('adminx_content')

<!--- Footer --->
```

The `adminx_layout` section contains page body content. You should yield this in your layout.

## 2. Page title
You should set title of page like this example:

```blade
<!--- ... --->

<title>{{ $core->getTitle() }} - @yield('adminx_title')</title>

<!--- ... --->
```

The `$core->getTitle()` returns admin panel base title and `@yield('adminx_title')` returns current page title.

Also you can use another `$core` methods (That's an instance from `\Adminx\Core` class).

## 3. Urls
To generate urls in admin panel, you can use `$core->url('...')` method.

for example:

```blade
<a href="{{ $core->url('/') }}">Link to admin panel main page</a>
```

Actually, the `$core->url()` method returns something like this: `<admin-panel-base-route>/<the-route>`. for example `$core->url('/some/place')` returns `/admin/some/place` (the `/admin` will be replaced with the base url you have set using method `register`).

## 4. Menu
You can show admin panel menu with this structure:

```blade
@foreach($core->getMenu() as $item)
  @if($item['type'] === 'link')
    <li class="nav-item">
      <a class="nav-link" href="{{ $item['link'] }}" target="{{ $item['target'] }}">
      <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
    </li>
  @else
    @if($item['type'] === 'page' && $item['slug'] !== '.')
      <li class="nav-item">
        <a class="nav-link" href="{{ $core->url('page/' . $item['slug']) }}" target="{{ $item['target'] }}">
        <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
      </li>
    @else
      @if($item['type'] === 'model')
        <li class="nav-item">
          <a class="nav-link" href="{{ $core->url('model/' . $item['config']['slug']) }}" target="{{ $item['config']['target'] }}">
          <i class="{{ $item['config']['icon'] }}"></i><span>{{ $item['config']['title'] }}</span></a>
        </li>
      @endif
    @endif
  @endif
@endforeach
```

---

[Previous: Frontend language customization](02_lang.md) | [Next: Models](04_models.md)
