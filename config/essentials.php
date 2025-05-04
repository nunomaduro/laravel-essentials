<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Minimum Test Coverage
    |--------------------------------------------------------------------------
    |
    | This value is the minimum percentage of code coverage required for tests
    | to pass. If not set, the minimum_test value will be used as a fallback.
    | If the coverage is below this threshold, the test run will fail.
    |
    */
    'minimum_test_coverage' => 40,

    /*
    |--------------------------------------------------------------------------
    | Minimum Type Coverage
    |--------------------------------------------------------------------------
    |
    | This value is the minimum percentage of type coverage required for tests
    | to pass. If not set, the minimum_test value will be used as a fallback.
    | If the type coverage is below this threshold, the test run will fail.
    |
    */
    'minimum_type_coverage' => 100,

    /*
    |--------------------------------------------------------------------------
    | Minimum Test
    |--------------------------------------------------------------------------
    |
    | This value is the minimum percentage required for general test metrics.
    | It also serves as a fallback value for minimum_test_coverage and
    | minimum_type_coverage when those are not explicitly set.
    |
    */
    'minimum_test' => 40,
];
