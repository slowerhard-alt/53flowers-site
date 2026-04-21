<?php

namespace Itgalaxy\PluginCommon;

class PluginActionLinks
{
    /**
     * @var string
     */
    private $pluginFileName;

    /**
     * @var array
     */
    private $newLinks;

    /**
     * @see https://developer.wordpress.org/reference/hooks/plugin_action_links/
     *
     * @return void
     */
    public function __construct(string $pluginFileName, array $newLinks)
    {
        $this->pluginFileName = basename($pluginFileName);
        $this->newLinks = $newLinks;

        \add_filter('plugin_action_links', [$this, 'pluginActionLinks'], 10, 2);
    }

    /**
     * @param array  $actions
     * @param string $pluginFile
     *
     * @return array
     */
    public function pluginActionLinks(array $actions, string $pluginFile): array
    {
        if (strpos($pluginFile, $this->pluginFileName) === false) {
            return $actions;
        }

        foreach ($this->newLinks as $newLink) {
            $actions[] = $newLink;
        }

        return $actions;
    }
}
