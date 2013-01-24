Feature: error handling
    As a prototype editor
    I benefit from error handling possibilities
    To soften the impact of errors on the experience

Scenario: default 404 page
    When I am on "http://prontotype-bar.lo/this/path/does/not/exist"
    Then the "h4" element should contain "page not found"
    And  the "h4" element should not contain "overriden"

Scenario: overriden 404 page
    When I am on "http://prontotype-foo.lo/this/path/does/not/exist"
    Then the "h4" element should contain "page not found"
    And  the "h4" element should contain "overriden"

Scenario: default error page
    When I am on "http://prontotype-bar.lo/error-test"
    Then the "h4" element should contain "error has occurred"
    And  the "h4" element should not contain "overriden"

Scenario: overriden error page
    When I am on "http://prontotype-foo.lo/error-test"
    Then the "h4" element should contain "error has occurred"
    And  the "h4" element should contain "overriden"
