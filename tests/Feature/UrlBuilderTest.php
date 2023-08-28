<?php

use Assetplan\Imgproxy\ResizingType;
use Assetplan\Imgproxy\UrlBuilder;

it('can generate plain url', function () {

    $builder = new UrlBuilder(getUrl(), getKey(), getSalt());

    $url = $builder->setImageUrl('https://example.com/image.jpg')->insecure()->resize(100, 100, ResizingType::Fill)->getUrl();

    $this->assertEquals(getUrl().'/insecure/rs:fill:100:100/plain/https://example.com/image.jpg', $url);
});

it('can generate encoded url', function () {

    $builder = new UrlBuilder(getUrl(), getKey(), getSalt());

    $url = $builder->setImageUrl('https://example.com/image.jpg')
        ->insecure()
        ->encode()
        ->resize(100, 100, ResizingType::Fill)
        ->getUrl();

    $base64Url = base64_encode('https://example.com/image.jpg');
    $base64Url = rtrim(strtr($base64Url, '+/', '-_'), '=');

    $this->assertEquals(getUrl().'/insecure/rs:fill:100:100/'.$base64Url.'.jpg', $url);
});

it('can generate signed url', function () {

    $builder = new UrlBuilder(getUrl(), getKey(), getSalt());

    $signedUrl = $builder->setImageUrl(getExampleImageUrl())
        ->resize(100, 100, ResizingType::Fill)
        ->getUrl();

    $this->assertMatchesRegularExpression('/^'.preg_quote(getUrl(), '/').'\/[^\/]+\/rs:fill:100:100\/plain\/https:\/\/example.com\/image.jpg$/', $signedUrl);
});

it('can change format to jpg when jfif', function(){
    $builder = new UrlBuilder(getUrl(), getKey(), getSalt());

    $url = $builder->setImageUrl('https://example.com/image.jfif')
        ->insecure()
        ->encode()
        ->resize(100, 100, ResizingType::Fill)
        ->getUrl();

    $base64Url = base64_encode('https://example.com/image.jfif');
    $base64Url = rtrim(strtr($base64Url, '+/', '-_'), '=');

    $this->assertEquals(getUrl().'/insecure/rs:fill:100:100/'.$base64Url.'.jpg', $url);
});

function getExampleImageUrl(): string
{
    return 'https://example.com/image.jpg';
}

function getKey() : string
{
    return '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
}

function getSalt() : string
{
    return '0123456789abcdef0123456789abcdef';
}

function getUrl() : string {
    return 'https://imgproxy.example.com';
}
