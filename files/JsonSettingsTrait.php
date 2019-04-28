<?php

namespace App\Models\Traits;

use Dflydev\DotAccessData\Data;

trait JsonSettingsTrait {
    public function getSettings($key = null, $default = null)
    {
        if (is_string($this->settings)) {
            throw new \RuntimeException(get_class($this).'->settings field is a string, not array. Put protected $casts = [\'settings\'=>\'array\'] into the class declaration;');
        }

        if (!$key) $key = 'settings';

        // First, look if it's the database field, like 'id' or 'name'
        if (isset($this[$key])) {
            return $this[$key];
        }

        // If it's not, use dot.env.notation to pull settings
        // todo: does it need performance improvements? caching?
        $dotAccessor = new Data($this->settings);
        $result = $dotAccessor->get($key, null);
        if ($result !== null) return $result;

        // If not found, try to use the specified default value, if provided
        if ($default !== null) return $default;

        // Else try to provide value from default model instance
        if (method_exists($this, 'getDefault')) {
            /** @var JsonSettingsTrait $defaultOne */
            $defaultOne = $this->getDefault();
            // But first, check, maybe we are the default one. Avoid eternal recursion we must
            if ($this->id !== $defaultOne->id) {
                $result = $defaultOne->getSettings($key, null);
                if ($result !== null) return $result;
            }
        }

        if ($result /* still */ === null) {
            return null;
        }

        return null;
    }

    public function appendSettings($path, $value)
    {
        $settings = new Data($this->settings);
        $settings->append($path, $value);
        $this->settings = $settings->export();
    }

    public function setSettings($path, $value)
    {
        $settings = new Data($this->settings);
        $settings->set($path, $value);
        $this->settings = $settings->export();
    }

    public function removeSettings($path)
    {
        $settings = new Data($this->settings);
        $settings->remove($path);
        $this->settings = $settings->export();
    }

}
