<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stockout forecast lookback window
    |--------------------------------------------------------------------------
    |
    | Number of past days of sales used by StockForecastService to compute
    | the average daily sale rate behind the "rupture probable dans N jours"
    | estimate. Adjust to make the forecast more reactive (fewer days) or
    | more stable (more days) without touching any code.
    |
    */
    'forecast_lookback_days' => env('STOCK_FORECAST_LOOKBACK_DAYS', 28),

];
