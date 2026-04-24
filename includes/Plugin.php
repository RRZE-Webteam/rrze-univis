<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

/**
 * Plugin class
 *
 * This class provides methods to manage plugin information such as file path,
 * basename, directory, URL, and plugin data.
 */
class Plugin {
    protected $pluginFile;
    protected $basename;
    protected $directory;
    protected $url;
    protected $data;

    /**
     * Constructor
     */
    public function __construct(string $pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    /**
     * Load the plugin information
     */
    public function loaded(): void {
        $this->setBasename()
            ->setDirectory()
            ->setUrl()
            ->setData();
    }

    /**
     * Get the full path and filename of the plugin
     *
     * @return string The full path and filename
     */
    public function getFile(): string {
        return $this->pluginFile;
    }

    /**
     * Get the basename of the plugin
     *
     * @return string The basename
     */
    public function getBasename(): string {
        return $this->basename;
    }

    /**
     * Set the basename of the plugin
     *
     * @return object This Plugin object
     */
    public function setBasename(): object {
        $this->basename = plugin_basename($this->pluginFile);
        return $this;
    }

    /**
     * Get the filesystem directory path (with trailing slash) for the plugin
     *
     * @return string The filesystem directory path
     */
    public function getDirectory(): string {
        return $this->directory;
    }

    /**
     * Set the filesystem directory path (with trailing slash) for the plugin
     *
     * @return object This Plugin object
     */
    public function setDirectory(): object {
        $this->directory = rtrim(plugin_dir_path($this->pluginFile), '/') . '/';
        return $this;
    }

    /**
     * Get the filesystem directory path (with trailing slash) for the plugin
     *
     * @param string $path The path name
     * @return string The filesystem directory path
     */
    public function getPath(string $path = ''): string {
        $path = trim($path, '/');
        if ($path === '') {
            return $this->directory;
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== '') {
            return $this->directory . $path;
        }

        return $this->directory . $path . '/';
    }

    /**
     * Get the URL directory path (with trailing slash) for the plugin
     *
     * @param string $path The path name
     * @return string The URL directory path
     */
    public function getUrl(string $path = ''): string {
        $path = trim($path, '/');
        if ($path === '') {
            return $this->url;
        }

        if (pathinfo($path, PATHINFO_EXTENSION) !== '') {
            return $this->url . $path;
        }

        return $this->url . $path . '/';
    }

    /**
     * Set the URL directory path (with trailing slash) for the plugin
     *
     * @return object This Plugin object
     */
    public function setUrl(): object {
        $this->url = rtrim(plugin_dir_url($this->pluginFile), '/') . '/';
        return $this;
    }

    /**
     * Get the slug of the plugin
     *
     * @return string The slug
     */
    public function getSlug(): string {
        return sanitize_title(dirname($this->basename));
    }

    /**
     * Set the data of the plugin
     *
     * @return object This Plugin object
     */
    public function setData(): object {
        $this->data = get_plugin_data($this->pluginFile, false, false);
        return $this;
    }

    /**
     * Get the data of the plugin
     *
     * @return array The data
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Get the name of the plugin
     *
     * @return string The name
     */
    public function getName(): string {
        return $this->data['Name'];
    }

    /**
     * Get the version of the plugin
     *
     * @return string The version
     */
    public function getVersion(): string {
        return $this->data['Version'];
    }

    /**
     * Get the required WordPress version of the plugin
     *
     * @return string The required WordPress version
     */
    public function getRequiresWP(): string {
        return $this->data['RequiresWP'];
    }

    /**
     * Get the required PHP version of the plugin
     *
     * @return string The required PHP version
     */
    public function getRequiresPHP(): string {
        return $this->data['RequiresPHP'];
    }

    /**
     * __call method
     *
     * Method overloading.
     *
     * @param string $name The name of the method
     * @param array $arguments The arguments passed to the method
     * @return void
     */
    public function __call(string $name, array $arguments): void {
        if (!method_exists($this, $name)) {
            $message = sprintf('Call to undefined method %1$s::%2$s', __CLASS__, $name);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                throw new \Exception($message);
            }
        }
    }
}
