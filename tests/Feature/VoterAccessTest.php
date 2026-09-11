<?php

use App\Models\Voter;

test('voter dengan data valid bisa akses voting', function () {
    $voter = Voter::factory()->create([
        'nis' => '99999',
        'class_name' => 'X-A',
        'tanggal_lahir' => '2008-01-01',
        'status' => true,
    ]);

    $response = $this->post('/vote/access', [
        'class_name' => 'X-A',
        'nis' => '99999',
        'tanggal_lahir' => '2008-01-01',
    ]);

    $response->assertRedirect('/vote/osis');
});

test('voter dengan data salah ditolak', function () {
    $response = $this->post('/vote/access', [
        'class_name' => 'X-A',
        'nis' => 'salah',
        'tanggal_lahir' => '2008-01-01',
    ]);

    $response->assertSessionHasErrors('nis');
});

test('voter nonaktif ditolak', function () {
    Voter::factory()->create([
        'nis' => '88888',
        'class_name' => 'X-B',
        'tanggal_lahir' => '2008-02-02',
        'status' => false,
    ]);

    $response = $this->post('/vote/access', [
        'class_name' => 'X-B',
        'nis' => '88888',
        'tanggal_lahir' => '2008-02-02',
    ]);

    $response->assertSessionHasErrors('nis');
});