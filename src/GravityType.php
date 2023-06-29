<?php

namespace Assetplan\Imgproxy;

enum GravityType: string
{
    case North = 'no';
    case South = 'so';
    case East = 'ea';
    case West = 'we';
    case NorthEast = 'noea';
    case NorthWest = 'nowe';
    case SouthEast = 'soea';
    case SouthWest = 'sowe';
    case Center = 'ce';
    case Smart = 'sm';
}
