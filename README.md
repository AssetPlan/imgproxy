# Getting Started

```bash
composer require assetplan/imgproxy
```

# Building the URL
```php
<?php

$key = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
$salt = '0123456789abcdef0123456789abcdef';

$builder = new Assetplan\Imgproxy\UrlBuilder('https://imgproxy.example.com', $key, $salt);

$url = $builder->setImageUrl('https://example.com/image.jpg')->insecure()->resize(100, 100, ResizingType::Fill)->getUrl();

// https://imgproxy.example.com/insecure/rs:fill:100:100/plain/https://example.com/image.jpg
```
