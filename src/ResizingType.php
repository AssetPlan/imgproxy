<?php

namespace Assetplan\Imgproxy;

enum ResizingType: string
{
    case Fit = 'fit';
    case Fill = 'fill';
    case FillDown = 'fill-down';
    case Force = 'force';
    case Auto = 'auto';
}
