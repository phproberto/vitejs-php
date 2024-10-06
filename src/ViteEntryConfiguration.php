<?php

namespace Phproberto\Vite;

use JsonSerializable;

final class ViteEntryConfiguration implements JsonSerializable{
    /**
     * @var array
     */
    protected $data;

    const DEFAULT_INTERNAL_HOST = 'http://localhost:5173';
    const DEFAULT_EXTERNAL_HOST = 'http://localhost:5173';
    const DEFAULT_MANIFEST = '/manifest.json';
    const MODE_DEVELOPMENT = 'development';
    const MODE_PRODUCTION = 'production';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getBasePath(): string
    {
        return $this->get('basePath') ?: '';
    }

    public function getBaseUrl(): string
    {
        return rtrim($this->get('baseUrl'), '/');
    }

    /**
     * @throws MissingEntryConfigurationDataException If manifest path is not set in the configuration
     */
    public function getManifestPath(): string
    {
        return $this->get('basePath') . $this->get('manifest', self::DEFAULT_MANIFEST);
    }

    public function getMode(): string
    {
        return $this->get('mode', self::MODE_DEVELOPMENT);
    }

    public function getExternalViteHost(): string
    {
        return $this->get('externalHost', self::DEFAULT_EXTERNAL_HOST);
    }

    public function getInternalViteHost(): string
    {
        return $this->get('internalHost', self::DEFAULT_INTERNAL_HOST);
    }

    public function get(string $property, $default = null)
    {
        return array_key_exists($property, $this->data) ? $this->data[$property] : $default;
    }

    public function getRawData(): array
    {
        return $this->data;
    }

    public function isDevelopment(): bool
    {
        return $this->getMode() === self::MODE_DEVELOPMENT;
    }

    public function isProduction(): bool
    {
        return $this->getMode() === self::MODE_PRODUCTION;
    }

    public function toArray()
    {
        // Merge data so we inherit any other custom setting
        return array_merge(
    $this->data, [
                'baseUrl'      => $this->getBaseUrl(),
                'basePath'     => $this->getBasePath(),
                'externalHost' => $this->getExternalViteHost(),
                'internalHost' => $this->getInternalViteHost(),
                'mode'         => $this->getMode(),
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}