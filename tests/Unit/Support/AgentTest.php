<?php

use Common\Support\Agent;

test('it should get agent (Chrome)', function () {
    $chrome = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_9) AppleWebKit/5351 (KHTML, like Gecko) Chrome/38.0.872.0 Mobile Safari/5351';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($chrome));

    expect($agent->isDesktop())->toBeTrue();
    expect($agent->platform())->toBe('OS X');
    expect($agent->browser())->toBe('Chrome');
});

test('it should get agent (Firefox)', function () {
    $firefox = 'Mozilla/5.0 (Windows CE; sl-SI; rv:1.9.2.20) Gecko/20141005 Firefox/37.0';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($firefox));

    expect($agent->isDesktop())->toBeTrue();
    expect($agent->browser())->toBe('Firefox');
    expect($agent->platform())->toBe('Windows');
});

test('it should get agent (Safari)', function () {
    $safari = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_4 rv:3.0; sl-SI) AppleWebKit/531.21.5 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.5';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($safari));

    expect($agent->isDesktop())->toBeTrue();
    expect($agent->platform())->toBe('OS X');
    expect($agent->browser())->toBe('Safari');
});

test('it should get agent (Opera)', function () {
    $opera = 'Opera/8.99 (X11; Linux i686; en-US) Presto/2.12.290 Version/10.00';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($opera));

    expect($agent->isDesktop())->toBeTrue();
    expect($agent->browser())->toBe('Opera');
    expect($agent->platform())->toBe('Linux');
});

test('it should get agent (IE)', function () {
    $internetExplorer = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows 98; Win 9x 4.90; Trident/4.0)';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($internetExplorer));

    expect($agent->browser())->toBe('IE');
    expect($agent->isDesktop())->toBeTrue();
    expect($agent->platform())->toBe('Windows');
});

test('it should get agent (Edge)', function () {
    $msedge = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_0 like Mac OS X) AppleWebKit/537.0 (KHTML, like Gecko) Version/15.0 EdgiOS/81.01104.79 Mobile/15E148 Safari/537.0';

    $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($msedge));

    expect($agent->browser())->toBe('Edge');
    expect($agent->platform())->toBe('iOS');
    expect($agent->isDesktop())->toBeFalse();
});
