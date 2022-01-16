<?php

namespace Adminx\Plugins\Builtin;

use Adminx\Plugins\IPlugin;
use Adminx\Core;

class FileManagerPlugin implements IPlugin
{
    public function run(Core $admin, array $options = [])
    {
        if (!(isset($options['access_middleware']) && $options['access_middleware'] instanceof \Closure))
        {
            $options['access_middleware'] = function () { return true; };
        }

        if (!call_user_func_array($options['access_middleware'], [auth()->user()])) {
            return;
        }

        $admin->addPage($admin->getWord('file-manager.page-title', 'File Manager'), 'file-manager', function() {
            return 'welcome to file manager';
        });
    }
}
