Feature: multiprototypes
    As a prototype editor
    I can easily run multiple prototype on a same prontotype instance
    In order to access useful functionalities

Scenario: run test prototypes
    Given I am on "http://prontotype-bar.lo"
    Then the "h1" element should contain "Prontotype is up and running."
    Given I am on "http://prontotype-foo.lo"
    Then the "h1" element should contain "Test Prototype"
