<?php

namespace Adminx;

/**
 * The Adminx Core
 */
class Core
{
    /**
     * Title of the admin panel
     */
    protected string $title = 'Adminx Panel';

    /**
     * Sets the title of admin panel
     */
    public function set_title(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns title of admin panel
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Copyright message of the admin panel
     */
    protected string $copyright = 'Copyright';

    /**
     * Sets the title of admin panel
     */
    public function set_copyright(string $copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns Copyright message of admin panel
     */
    public function get_copyright()
    {
        return $this->copyright;
    }
}
