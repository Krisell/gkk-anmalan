<?php

test('records page can be viewed', function () {
    $this->get('/klubbrekord')->assertViewHas('results');
});
