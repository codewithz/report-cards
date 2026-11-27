<?php

test('it redirects the home page to the sections list', function () {
    $response = $this->get('/');

    $response->assertRedirect('/sections');
});
