<?php

use Kareylo\EntityRouting\EntityNaming;
use Kareylo\EntityRouting\Tests\Fixtures\BlogPost;

beforeEach(function () {
    $this->naming = new EntityNaming;
});

it('matches the camel case name of the entity class', function () {
    expect($this->naming->matches('blogPost', new BlogPost))->toBeTrue();
});

it('matches the snake case name of the entity class', function () {
    expect($this->naming->matches('blog_post', new BlogPost))->toBeTrue();
});

it('does not match another name', function () {
    expect($this->naming->matches('post', new BlogPost))->toBeFalse();
});

it('never matches an array entity', function () {
    expect($this->naming->matches('array', ['id' => 42]))->toBeFalse();
});
