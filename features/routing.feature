Feature: routing, pages, URL generation
    As a prototype editor
    I benefit from multiple routes to a specific page

Scenario: default page is index
    Given I am on "http://prontotype-foo.lo/"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "http://prontotype-foo.lo/index/"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "http://prontotype-foo.lo/sub/"
    Then the "h1" element should contain "sub-section"
    Given I am on "http://prontotype-foo.lo/sub/index/"
    Then the "h1" element should contain "sub-section"

Scenario: ending slash is optional
    Given I am on "http://prontotype-foo.lo/index"
    Then the "h1" element should contain "Test Prototype"
    Given I am on "http://prontotype-foo.lo/sub"
    Then the "h1" element should contain "sub-section"
    Given I am on "http://prontotype-foo.lo/sub/index"
    Then the "h1" element should contain "sub-section"

Scenario Outline: Page title
    Given I am on "http://prontotype-foo.lo/sub/<title>"
    Then the "h1" element should contain "sub-<title>"

    Examples:
        | title   |
        | yet     |
        | another |
        | page    |

Scenario Outline: Page linking
    Given I am on "http://prontotype-foo.lo/sub"
    Then the "p.<title>" element should contain "sub/<title>"

    Examples:
        | title   |
        | yet     |

