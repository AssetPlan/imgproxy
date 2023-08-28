<?php

namespace Assetplan\Imgproxy;

class UrlBuilder
{
    protected string $originalUrl;

    protected bool $isInsecure = false;

    protected bool $resize = false;

    protected bool $gravity = false;

    protected bool $plain = true;

    protected $width;

    protected $height;

    protected $resizingType;

    protected $gravityType;

    public function __construct(
        protected string $imgproxyUrl,
        protected string $imgproxyKey,
        protected string $imgproxySalt
    ) {
    }

    public function setImageUrl(string $url)
    {
        $this->originalUrl = $url;

        return $this;
    }

    public function insecure()
    {
        $this->isInsecure = true;

        return $this;
    }

    public function encode()
    {
        $this->plain = false;

        return $this;
    }

    public function resize(int $width, int $height, ResizingType $resizingType)
    {
        $this->resize = true;
        $this->width = $width;
        $this->height = $height;

        $this->resizingType = $resizingType;

        return $this;
    }

    public function gravity(GravityType $gravityType)
    {
        $this->gravity = true;
        $this->gravityType = $gravityType;

        return $this;
    }

    public function getUrl(): string
    {

        if ($this->isInsecure) {
            return $this->buildUrlFromParts($this->baseUrl(), $this->insecurePath());
        }

        return $this->buildUrlFromParts($this->baseUrl(), $this->securePath());
    }

    protected function buildPath(string $pathOptions, string $url)
    {
        $pathOptions = trim($pathOptions, '/');
        $url = trim($url, '/');

        return '/' . $this->buildUrlFromParts($pathOptions, $url);
    }

    protected function insecurePath(): string
    {
        $signature = 'insecure';
        $options = $this->buildPathOptions();
        if ($this->plain) {
            return $this->buildUrlFromParts($signature, $this->buildPath($options, 'plain/' . $this->originalUrl));
        }

        return $this->buildUrlFromParts($signature, $this->buildPath($options, $this->encodedUrl()));
    }

    protected function securePath(): string
    {
        $options = $this->buildPathOptions();
        if ($this->plain) {
            $path = $this->buildPath($options, 'plain/' . $this->originalUrl);
        } else {
            $path = $this->buildPath($options, $this->encodedUrl());
        }

        $signature = $this->sign($path);

        return $this->buildUrlFromParts($signature, $path);
    }

    protected function buildPathOptions(): string
    {
        $path = '';
        if ($this->resize) {
            $path = '/rs:' . $this->resizingType->value . ':' . $this->width . ':' . $this->height;
        }

        if ($this->gravity) {
            $path .= '/g:' . $this->gravityType->value;
        }

        return $path;
    }

    protected function encodedUrl()
    {
        $info = pathinfo($this->originalUrl);

        $extension = $info['extension'];

        if ($extension === 'jfif') {
            $extension = 'jpg';
        }

        $encodedUrl = rtrim(strtr(base64_encode($this->originalUrl), '+/', '-_'), '=');

        return $encodedUrl . '.' . $extension;
    }

    protected function sign(string $path): string
    {
        $keyBin = pack('H*', $this->imgproxyKey);
        $saltBin = pack('H*', $this->imgproxySalt);

        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $saltBin . $path, $keyBin, true)), '+/', '-_'), '=');

        return $signature;
    }

    protected function buildUrlFromParts(...$parts): string
    {
        $parts = array_map(function ($part) {
            return trim($part, '/');
        }, $parts);

        return implode('/', $parts);
    }

    protected function baseUrl(): string
    {
        return $this->imgproxyUrl;
    }
}
