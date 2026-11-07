<?php

test('the secretariat page is served at /sek', function () {
    $this->get('/sek')->assertOk()->assertSee('<gkk-secretariat>', false);
});
