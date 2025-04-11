<?php

it('marks dates as immutable', function (): void {
    $date = now();

    expect($date->isImmutable())->toBeTrue();
});
