Feature: routing, pages, URL generation
    As a prototype editor
    I benefit from multiple routes to a specific page

Scenario: default page is index
    Given I am on "/"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "/index/"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "/animals/"
    Then the "h1" element should contain "Animals"
    Given I am on "/animals/index/"
    Then the "h1" element should contain "Animals"

Scenario: ending slash is optional
    Given I am on "index"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "/animals"
    Then the "h1" element should contain "Animals"
    Given I am on "/animals/index"
    Then the "h1" element should contain "Animals"

Scenario Outline: Page ordering
    Given I am on "/animals/<class>"
    Then the "h1" element should contain "<class>"

    Examples:
        | class |
        | mammals |
        | reptiles |
        | birds |

Scenario Outline: Page IDs
    Given I am on "/animals/mammals/<species>"
    Then the "h1" element should contain "<species>"

    Examples:
        | id     | species |
        | 11     | cats    |
        | 011    | dogs    |
        | horses | horses  |

Scenario: page ordering AND IDs
    Given I am on "/animals/mammals/rats"
    Then the "h1" element should contain "Rats"

Scenario Outline: Page linking
    Given I am on "/animals"
    Then the "p.<species>" element should contain "animals/mammals/<species>"

    Examples:
        | species |
        | cats    |
        | rats    |

