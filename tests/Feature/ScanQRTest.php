<?php

test('guests can access the scan QR page', function () {
    $response = $this->get(route('scan'));

    $response->assertOk();
    $response->assertInertia(fn($page) => $page->component('ScanQR'));
});

test('guests can access the print page without shop UUID', function () {
    $response = $this->get(route('print'));

    $response->assertOk();
    $response->assertInertia(fn($page) => $page
            ->component('PrintUpload')
            ->where('shop', null)
            ->where('shopUuid', null)
    );
});

test('guests can access the print page with shop UUID', function () {
    $shop = \App\Models\Shop::factory()->create();

    $response = $this->get(route('print', $shop->uuid));

    $response->assertOk();
    $response->assertInertia(fn($page) => $page
            ->component('PrintUpload')
            ->has('shop')
            ->where('shopUuid', $shop->uuid)
    );
});
