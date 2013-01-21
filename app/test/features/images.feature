Feature: placeholder images helper
    As a prototype editor
    I can insert placeholder images

Scenario Outline: image URL generation
    Given I am on "http://prontotype-foo.lo/images"
    Then the "#<service>-container" element should contain "<service>"
    And the "#<service>-container" element should contain "<url>"
    And the "#<service>-container" element should contain "hello world"

    Examples:
        | service      | url                                              |
        | dummyimage   | http://dummyimage.com/300x150/000000/E66F05.png  |
        | placeholdit  | http://placehold.it/300x150/000000/E66F05.png    |
        | lorempixel   | http://lorempixel.com/300/150/people/hello world |
